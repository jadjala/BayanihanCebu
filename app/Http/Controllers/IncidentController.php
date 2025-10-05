<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isOfficial()) {
            $reports = IncidentReport::with('user')->orderBy('created_at', 'desc')->paginate(15);
        } else {
            $reports = IncidentReport::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(15);
        }
        
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'Pending';
        $validated['urgency_level'] = $this->detectUrgency($validated['description']);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('reports', 'public');
        }

        IncidentReport::create($validated);

        return redirect()->route('reports.index')->with('success', 'Report submitted successfully!');
    }

    public function show(IncidentReport $report)
    {
        // Check authorization
        if (!Auth::user()->isAdmin() && !Auth::user()->isOfficial() && $report->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reports.show', compact('report'));
    }

    public function edit(IncidentReport $report)
    {
        // Only owner can edit, and only if pending
        if ($report->user_id !== Auth::id() || $report->status !== 'Pending') {
            abort(403);
        }

        return view('reports.edit', compact('report'));
    }

    public function update(Request $request, IncidentReport $report)
    {
        // Only owner can update, and only if pending
        if ($report->user_id !== Auth::id() || $report->status !== 'Pending') {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['urgency_level'] = $this->detectUrgency($validated['description']);

        if ($request->hasFile('photo')) {
            if ($report->photo_path) {
                Storage::disk('public')->delete($report->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('reports', 'public');
        }

        $report->update($validated);

        return redirect()->route('reports.show', $report)->with('success', 'Report updated successfully!');
    }

    public function destroy(IncidentReport $report)
    {
        // Only owner can delete, and only if pending
        if ($report->user_id !== Auth::id() || $report->status !== 'Pending') {
            abort(403);
        }

        if ($report->photo_path) {
            Storage::disk('public')->delete($report->photo_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Report deleted successfully!');
    }

    public function verify(Request $request, IncidentReport $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:Verified,Resolved',
            'comment' => 'nullable|string',
        ]);

        $report->update([
            'status' => $validated['status'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'official_comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->route('reports.show', $report)->with('success', 'Report updated successfully!');
    }

    private function detectUrgency($description)
    {
        $description = strtolower($description);
        
        $criticalKeywords = ['fire', 'emergency', 'severe', 'critical', 'urgent', 'immediate', 'death', 'fatal'];
        $highKeywords = ['flood', 'accident', 'crime', 'injured', 'danger', 'threatening'];
        $mediumKeywords = ['problem', 'issue', 'concern', 'damage'];
        
        foreach ($criticalKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'Critical';
            }
        }
        
        foreach ($highKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'High';
            }
        }
        
        foreach ($mediumKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                return 'Medium';
            }
        }
        
        return 'Low';
    }
}