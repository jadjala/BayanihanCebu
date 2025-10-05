{{-- resources/views/donations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Donation')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Add New Donation</h1>
        <p class="text-gray-600 mt-2">Record your donation to help the barangay community.</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('donations.store') }}">
            @csrf

            {{-- Type --}}
            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Donation Type <span class="text-red-500">*</span>
                </label>
                <select name="type" id="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                    <option value="">Select Type</option>
                    <option value="Cash" {{ old('type') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Food" {{ old('type') === 'Food' ? 'selected' : '' }}>Food</option>
                    <option value="Clothing" {{ old('type') === 'Clothing' ? 'selected' : '' }}>Clothing</option>
                    <option value="Medical Supplies" {{ old('type') === 'Medical Supplies' ? 'selected' : '' }}>Medical Supplies</option>
                    <option value="School Supplies" {{ old('type') === 'School Supplies' ? 'selected' : '' }}>School Supplies</option>
                    <option value="Construction Materials" {{ old('type') === 'Construction Materials' ? 'selected' : '' }}>Construction Materials</option>
                    <option value="Other" {{ old('type') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quantity --}}
            <div class="mb-6">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" step="0.01" min="0.01" required
                       placeholder="e.g., 100 (for items), 1 (for cash)"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror">
                @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Number of items or units</p>
            </div>

            {{-- Value --}}
            <div class="mb-6">
                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                    Monetary Value (₱) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="value" id="value" value="{{ old('value') }}" step="0.01" min="0" required
                       placeholder="e.g., 5000.00"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('value') border-red-500 @enderror">
                @error('value')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Estimated monetary value of the donation</p>
            </div>

            {{-- Destination --}}
            <div class="mb-6">
                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
                    Destination (Optional)
                </label>
                <input type="text" name="destination" id="destination" value="{{ old('destination') }}"
                       placeholder="e.g., Purok 5, Fire Victims, General Distribution"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('destination') border-red-500 @enderror">
                @error('destination')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Specify if this donation is for a specific area or incident</p>
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Notes (Optional)
                </label>
                <textarea name="notes" id="notes" rows="4"
                          placeholder="Any additional information about the donation..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                    Submit Donation
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- resources/views/donations/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Donation Details')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('donations.index') }}" class="text-blue-600 hover:underline flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Donations
        </a>
    </div>

    {{-- Donation Header --}}
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $donation->type }}</h1>
                <p class="text-gray-600 mt-2">Donated by {{ $donation->donor->name }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($donation->status === 'Pending') bg-yellow-100 text-yellow-800
                @elseif($donation->status === 'Verified') bg-blue-100 text-blue-800
                @else bg-green-100 text-green-800 @endif">
                {{ $donation->status }}
            </span>
        </div>

        {{-- Donation Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <p class="text-2xl font-bold text-gray-900">{{ $donation->quantity }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Monetary Value</label>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($donation->value, 2) }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Remaining Quantity</label>
                <p class="text-xl font-semibold text-blue-600">{{ $donation->remaining_quantity }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                <p class="text-gray-900">{{ $donation->destination ?? 'General Distribution' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Submitted</label>
                <p class="text-gray-900">{{ $donation->created_at->format('M d, Y h:i A') }}</p>
            </div>
            @if($donation->verified_at)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Verified By</label>
                <p class="text-gray-900">{{ $donation->verifier->name }} on {{ $donation->verified_at->format('M d, Y') }}</p>
            </div>
            @endif
        </div>

        {{-- Notes --}}
        @if($donation->notes)
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900">{{ $donation->notes }}</p>
            </div>
        </div>
        @endif

        {{-- Actions for Donor --}}
        @if(auth()->id() === $donation->donor_id && $donation->status === 'Pending')
        <div class="mt-6 pt-6 border-t flex space-x-4">
            <a href="{{ route('donations.edit', $donation) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Edit Donation
            </a>
            <form method="POST" action="{{ route('donations.destroy', $donation) }}" onsubmit="return confirm('Are you sure you want to delete this donation?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                    Delete Donation
                </button>
            </form>
        </div>
        @endif

        {{-- Actions for Officials --}}
        @if((auth()->user()->isOfficial() || auth()->user()->isAdmin()) && $donation->status === 'Pending')
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Official Actions</h3>
            <form method="POST" action="{{ route('donations.verify', $donation) }}">
                @csrf
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Verify Donation</label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="Verified">Verify Donation</option>
                    </select>
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Verify Donation
                </button>
            </form>
        </div>
        @endif

        {{-- Distribution Button for Officials --}}
        @if((auth()->user()->isOfficial() || auth()->user()->isAdmin()) && $donation->status === 'Verified' && $donation->remaining_quantity > 0)
        <div class="mt-6 pt-6 border-t">
            <a href="{{ route('donations.distribute.show', $donation) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 inline-block">
                Record Distribution
            </a>
        </div>
        @endif
    </div>

    {{-- Distribution History --}}
    @if($donation->logs->count() > 0)
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 border-b bg-green-50">
            <h2 class="text-xl font-bold text-green-900 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Distribution Transparency Ledger
            </h2>
            <p class="text-sm text-green-800 mt-1">Track how this donation has been distributed</p>
        </div>

        @foreach($donation->logs as $log)
        <div class="p-6 border-b hover:bg-gray-50">
            <div class="flex items-start">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-gray-900">Distributed to: {{ $log->distributed_to }}</h3>
                            <p class="text-sm text-gray-600">By {{ $log->official->name }}</p>
                        </div>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $log->quantity_distributed }} units
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">{{ $log->created_at->format('F d, Y h:i A') }}</p>
                    @if($log->remarks)
                    <div class="bg-gray-50 rounded p-3 mt-2">
                        <p class="text-sm text-gray-700 italic">"{{ $log->remarks }}"</p>
                    </div>
                    @endif
                    @if($log->proof_photo)
                    <div class="mt-3">
                        <a href="{{ Storage::url($log->proof_photo) }}" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            View Proof Photo
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="p-4 bg-gray-50 text-center">
            <p class="text-sm text-gray-600">
                Total Distributed: <strong>{{ $donation->logs->sum('quantity_distributed') }}</strong> / 
                Total Quantity: <strong>{{ $donation->quantity }}</strong>
            </p>
        </div>
    </div>
    @endif

    {{-- Blockchain Verification (if enabled) --}}
    @if($donation->status === 'Verified' && $donation->blockchainHash)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="font-bold text-blue-900 mb-2">Blockchain Verified</h3>
                <p class="text-sm text-blue-800 mb-2">This donation has been cryptographically verified for transparency and immutability.</p>
                <p class="text-xs text-blue-700 font-mono break-all bg-white p-2 rounded">
                    Hash: {{ $donation->blockchainHash->hash_value }}
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

{{-- resources/views/donations/distribute.blade.php --}}
@extends('layouts.app')

@section('title', 'Record Distribution')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Record Donation Distribution</h1>
        <p class="text-gray-600 mt-2">Log the distribution of: <strong>{{ $donation->type }}</strong></p>
        <p class="text-sm text-gray-500 mt-1">Remaining quantity: <strong>{{ $donation->remaining_quantity }}</strong></p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('donations.distribute', $donation) }}" enctype="multipart/form-data">
            @csrf

            {{-- Distributed To --}}
            <div class="mb-6">
                <label for="distributed_to" class="block text-sm font-medium text-gray-700 mb-2">
                    Distributed To <span class="text-red-500">*</span>
                </label>
                <input type="text" name="distributed_to" id="distributed_to" value="{{ old('distributed_to') }}" required
                       placeholder="e.g., Juan Dela Cruz, Purok 5 Fire Victims"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('distributed_to') border-red-500 @enderror">
                @error('distributed_to')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quantity Distributed --}}
            <div class="mb-6">
                <label for="quantity_distributed" class="block text-sm font-medium text-gray-700 mb-2">
                    Quantity Distributed <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity_distributed" id="quantity_distributed" value="{{ old('quantity_distributed') }}" 
                       step="0.01" min="0.01" max="{{ $donation->remaining_quantity }}" required
                       placeholder="Maximum: {{ $donation->remaining_quantity }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity_distributed') border-red-500 @enderror">
                @error('quantity_distributed')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Proof Photo --}}
            <div class="mb-6">
                <label for="proof_photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Proof Photo (Optional but Recommended)
                </label>
                <input type="file" name="proof_photo" id="proof_photo" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proof_photo') border-red-500 @enderror">
                @error('proof_photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Upload a photo as proof of distribution for transparency</p>
            </div>

            {{-- Remarks --}}
            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                    Remarks (Optional)
                </label>
                <textarea name="remarks" id="remarks" rows="4"
                          placeholder="Any additional notes about this distribution..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('remarks') border-red-500 @enderror">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                    Record Distribution
                </button>
                <a href="{{ route('donations.show', $donation) }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection