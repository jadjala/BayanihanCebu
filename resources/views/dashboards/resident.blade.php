{{-- resources/views/dashboards/resident.blade.php --}}
@extends('layouts.app')

@section('title', 'Resident Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600 mt-2">Resident Dashboard</p>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Reports</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalReports }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingReports }}</p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Verified</p>
                <p class="text-2xl font-bold text-blue-600">{{ $verifiedReports }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Resolved</p>
                <p class="text-2xl font-bold text-green-600">{{ $resolvedReports }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('reports.create') }}" class="flex items-center p-4 border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition">
            <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <div>
                <p class="font-semibold text-gray-800">Submit New Report</p>
                <p class="text-sm text-gray-600">Report an incident in your area</p>
            </div>
        </a>

        <a href="{{ route('reports.index') }}" class="flex items-center p-4 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <div>
                <p class="font-semibold text-gray-800">View My Reports</p>
                <p class="text-sm text-gray-600">Track your submitted reports</p>
            </div>
        </a>
    </div>
</div>

{{-- Recent Reports --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b">
        <h2 class="text-xl font-bold text-gray-800">Recent Reports</h2>
    </div>
    
    @forelse($recentReports as $report)
        <div class="p-6 border-b hover:bg-gray-50">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <a href="{{ route('reports.show', $report) }}" class="text-lg font-semibold text-blue-600 hover:underline">
                        {{ $report->title }}
                    </a>
                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($report->description, 100) }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $report->category }}</span>
                        <span class="text-xs px-2 py-1 rounded
                            @if($report->urgency_level === 'Critical') bg-red-100 text-red-800
                            @elseif($report->urgency_level === 'High') bg-orange-100 text-orange-800
                            @elseif($report->urgency_level === 'Medium') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ $report->urgency_level }}
                        </span>
                    </div>
                </div>
                <span class="ml-4 px-3 py-1 text-sm rounded-full
                    @if($report->status === 'Pending') bg-yellow-100 text-yellow-800
                    @elseif($report->status === 'Verified') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ $report->status }}
                </span>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-gray-500">
            <p>No reports yet. Submit your first report to get started!</p>
        </div>
    @endforelse
    
    @if($recentReports->count() > 0)
        <div class="p-4 text-center">
            <a href="{{ route('reports.index') }}" class="text-blue-600 hover:underline">View All Reports →</a>
        </div>
    @endif
</div>
@endsection