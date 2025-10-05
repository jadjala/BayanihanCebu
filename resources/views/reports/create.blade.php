{{-- resources/views/reports/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Submit Incident Report')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Submit Incident Report</h1>
        <p class="text-gray-600 mt-2">Report an incident in your barangay. Your report will be reviewed by officials.</p>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Report Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                       placeholder="e.g., Fire incident on Main Street"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>
                <select name="category" id="category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-500 @enderror">
                    <option value="">Select Category</option>
                    <option value="Fire" {{ old('category') === 'Fire' ? 'selected' : '' }}>Fire</option>
                    <option value="Flood" {{ old('category') === 'Flood' ? 'selected' : '' }}>Flood</option>
                    <option value="Medical Emergency" {{ old('category') === 'Medical Emergency' ? 'selected' : '' }}>Medical Emergency</option>
                    <option value="Crime" {{ old('category') === 'Crime' ? 'selected' : '' }}>Crime</option>
                    <option value="Accident" {{ old('category') === 'Accident' ? 'selected' : '' }}>Accident</option>
                    <option value="Infrastructure" {{ old('category') === 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                    <option value="Noise Complaint" {{ old('category') === 'Noise Complaint' ? 'selected' : '' }}>Noise Complaint</option>
                    <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Location --}}
            <div class="mb-6">
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Location <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" required
                       placeholder="e.g., Purok 5, Brgy. San Isidro"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror">
                @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="6" required
                          placeholder="Provide detailed information about the incident. Include what happened, when it occurred, and any other relevant details."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">
                    <strong>Note:</strong> Urgency level will be automatically detected based on your description.
                </p>
            </div>

            {{-- Photo Upload --}}
            <div class="mb-6">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Photo Evidence (Optional)
                </label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('photo') border-red-500 @enderror">
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB. Supported formats: JPG, PNG, GIF</p>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Submit Report
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- resources/views/reports/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:underline flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Reports
        </a>
    </div>

    {{-- Report Header --}}
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $report->title }}</h1>
                <p class="text-gray-600 mt-2">Reported by {{ $report->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($report->status === 'Pending') bg-yellow-100 text-yellow-800
                    @elseif($report->status === 'Verified') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ $report->status }}
                </span>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($report->urgency_level === 'Critical') bg-red-100 text-red-800
                    @elseif($report->urgency_level === 'High') bg-orange-100 text-orange-800
                    @elseif($report->urgency_level === 'Medium') bg-yellow-100 text-yellow-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ $report->urgency_level }} Urgency
                </span>
            </div>
        </div>

        {{-- Report Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <p class="text-gray-900">{{ $report->category }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <p class="text-gray-900">{{ $report->location }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Submitted</label>
                <p class="text-gray-900">{{ $report->created_at->format('M d, Y h:i A') }}</p>
            </div>
            @if($report->verified_at)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Verified By</label>
                <p class="text-gray-900">{{ $report->verifier->name }} on {{ $report->verified_at->format('M d, Y') }}</p>
            </div>
            @endif
        </div>

        {{-- Description --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
            </div>
        </div>

        {{-- Photo Evidence --}}
        @if($report->photo_path)
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Photo Evidence</label>
            <img src="{{ Storage::url($report->photo_path) }}" alt="Report Photo" class="max-w-full rounded-lg shadow">
        </div>
        @endif

        {{-- Official Comment --}}
        @if($report->official_comment)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <label class="block text-sm font-medium text-blue-900 mb-2">Official Response</label>
            <p class="text-blue-900">{{ $report->official_comment }}</p>
        </div>
        @endif

        {{-- Actions for Report Owner --}}
        @if(auth()->id() === $report->user_id && $report->status === 'Pending')
        <div class="mt-6 pt-6 border-t flex space-x-4">
            <a href="{{ route('reports.edit', $report) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Edit Report
            </a>
            <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Are you sure you want to delete this report?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                    Delete Report
                </button>
            </form>
        </div>
        @endif

        {{-- Actions for Officials --}}
        @if((auth()->user()->isOfficial() || auth()->user()->isAdmin()) && $report->status === 'Pending')
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Official Actions</h3>
            <form method="POST" action="{{ route('reports.verify', $report) }}">
                @csrf
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="Verified">Verify Report</option>
                        <option value="Resolved">Mark as Resolved</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Official Comment (Optional)</label>
                    <textarea name="comment" id="comment" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                              placeholder="Add any comments or actions taken..."></textarea>
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Submit Action
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection