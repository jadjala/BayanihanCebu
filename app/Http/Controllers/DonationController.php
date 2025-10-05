<?php
// app/Http/Controllers/DonationController.php
namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\Notification;
use App\Models\BlockchainHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    // Display list of donations (filtered by role)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Donation::with(['donor', 'verifier', 'logs']);

        // Donors only see their own donations
        if ($user->isDonor()) {
            $query->where('donor_id', $user->id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $donations = $query->latest()->paginate(15);

        return view('donations.index', compact('donations'));
    }

    // Show form to create new donation
    public function create()
    {
        return view('donations.create');
    }

    // Store new donation
    public function store(Request $request)
    {
        // Validate donation data
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'value' => ['required', 'numeric', 'min:0'],
            'destination' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Create the donation
        $donation = Donation::create([
            'donor_id' => Auth::id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'value' => $validated['value'],
            'destination' => $validated['destination'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Pending',
        ]);

        // Notify officials about new donation
        $this->notifyOfficials($donation);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Donation submitted successfully! Awaiting verification.');
    }

    // Display specific donation
    public function show(Donation $donation)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->isDonor() && $donation->donor_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $donation->load(['donor', 'verifier', 'logs.official']);

        return view('donations.show', compact('donation'));
    }

    // Show form to edit donation (only for pending donations by donor)
    public function edit(Donation $donation)
    {
        // Only donor can edit pending donations
        if ($donation->donor_id !== Auth::id() || $donation->status !== 'Pending') {
            abort(403, 'Cannot edit this donation');
        }

        return view('donations.edit', compact('donation'));
    }

    // Update donation
    public function update(Request $request, Donation $donation)
    {
        // Only donor can update pending donations
        if ($donation->donor_id !== Auth::id() || $donation->status !== 'Pending') {
            abort(403, 'Cannot update this donation');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'value' => ['required', 'numeric', 'min:0'],
            'destination' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $donation->update($validated);

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Donation updated successfully!');
    }

    // Official: Verify donation
    public function verify(Request $request, Donation $donation)
    {
        // Only officials and admins can verify
        if (!Auth::user()->isOfficial() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Verified,Distributed'],
        ]);

        // Update donation status
        $donation->update([
            'status' => $validated['status'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Create blockchain hash for verified donation
        if ($validated['status'] === 'Verified') {
            $this->createBlockchainHash($donation);
        }

        // Notify donor
        Notification::create([
            'user_id' => $donation->donor_id,
            'type' => 'donation_verified',
            'title' => 'Donation ' . $validated['status'],
            'message' => "Your donation of {$donation->type} has been {$validated['status']}.",
            'data' => ['donation_id' => $donation->id],
        ]);

        return back()->with('success', 'Donation status updated successfully');
    }

    // Official: Show form to distribute donation
    public function showDistribute(Donation $donation)
    {
        // Only officials can distribute
        if (!Auth::user()->isOfficial() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        // Donation must be verified first
        if ($donation->status !== 'Verified') {
            return back()->with('error', 'Donation must be verified before distribution');
        }

        return view('donations.distribute', compact('donation'));
    }

    // Official: Record donation distribution
    public function distribute(Request $request, Donation $donation)
    {
        // Only officials and admins can distribute
        if (!Auth::user()->isOfficial() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        $validated = $request->validate([
            'distributed_to' => ['required', 'string', 'max:255'],
            'quantity_distributed' => ['required', 'numeric', 'min:0.01', 'max:' . $donation->remaining_quantity],
            'proof_photo' => ['nullable', 'image', 'max:2048'],
            'remarks' => ['nullable', 'string'],
        ]);

        // Handle proof photo upload
        $photoPath = null;
        if ($request->hasFile('proof_photo')) {
            $photoPath = $request->file('proof_photo')->store('distribution_proofs', 'public');
        }

        // Create distribution log
        $log = DonationLog::create([
            'donation_id' => $donation->id,
            'official_id' => Auth::id(),
            'distributed_to' => $validated['distributed_to'],
            'quantity_distributed' => $validated['quantity_distributed'],
            'proof_photo' => $photoPath,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Check if fully distributed
        if ($donation->remaining_quantity <= 0) {
            $donation->update(['status' => 'Distributed']);
            
            // Notify donor about completion
            Notification::create([
                'user_id' => $donation->donor_id,
                'type' => 'donation_distributed',
                'title' => 'Donation Fully Distributed',
                'message' => "Your donation of {$donation->type} has been fully distributed.",
                'data' => ['donation_id' => $donation->id],
            ]);
        }

        return redirect()->route('donations.show', $donation)
            ->with('success', 'Distribution recorded successfully!');
    }

    // Display donation history with transparency ledger
    public function history(Donation $donation)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->isDonor() && $donation->donor_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $donation->load(['donor', 'verifier', 'logs.official', 'blockchainHash']);

        return view('donations.history', compact('donation'));
    }

    // Helper: Notify officials about new donation
    private function notifyOfficials(Donation $donation)
    {
        $officials = \App\Models\User::whereIn('role', ['official', 'admin'])->get();

        foreach ($officials as $official) {
            Notification::create([
                'user_id' => $official->id,
                'type' => 'new_donation',
                'title' => 'New Donation Received',
                'message' => "New donation: {$donation->type} (Qty: {$donation->quantity})",
                'data' => ['donation_id' => $donation->id],
            ]);
        }
    }

    // Helper: Create blockchain hash for verified donation
    private function createBlockchainHash(Donation $donation)
    {
        $data = [
            'id' => $donation->id,
            'type' => $donation->type,
            'quantity' => $donation->quantity,
            'value' => $donation->value,
            'status' => $donation->status,
            'verified_by' => $donation->verified_by,
            'verified_at' => $donation->verified_at,
        ];

        // Get previous hash for this table
        $previousHash = BlockchainHash::where('table_name', 'donations')
            ->latest()
            ->value('hash_value');

        $hash = BlockchainHash::generateHash($data, $previousHash);

        BlockchainHash::create([
            'table_name' => 'donations',
            'record_id' => $donation->id,
            'hash_value' => $hash,
            'previous_hash' => $previousHash,
        ]);
    }

    // Delete donation
    public function destroy(Donation $donation)
    {
        // Only donor or admin can delete pending donations
        if ($donation->donor_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action');
        }

        if ($donation->status !== 'Pending') {
            return back()->with('error', 'Cannot delete verified or distributed donations');
        }

        $donation->delete();

        return redirect()->route('donations.index')
            ->with('success', 'Donation deleted successfully');
    }
}