{{-- resources/views/dashboards/donor.blade.php --}}
@extends('layouts.app')

@section('title', 'Donor Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600 mt-2">Donor Dashboard - Track Your Impact</p>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Donations</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalDonations }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Value</p>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($totalValue, 2) }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingDonations }}</p>
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
                <p class="text-2xl font-bold text-blue-600">{{ $verifiedDonations }}</p>
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
                <p class="text-gray-500 text-sm">Distributed</p>
                <p class="text-2xl font-bold text-green-600">{{ $distributedDonations }}</p>
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
        <a href="{{ route('donations.create') }}" class="flex items-center p-4 border-2 border-green-600 rounded-lg hover:bg-green-50 transition">
            <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <div>
                <p class="font-semibold text-gray-800">Add New Donation</p>
                <p class="text-sm text-gray-600">Record your contribution</p>
            </div>
        </a>

        <a href="{{ route('donations.index') }}" class="flex items-center p-4 border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition">
            <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <div>
                <p class="font-semibold text-gray-800">View My Donations</p>
                <p class="text-sm text-gray-600">Track donation history</p>
            </div>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Recent Donations --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Recent Donations</h2>
        </div>
        
        @forelse($recentDonations as $donation)
            <div class="p-6 border-b hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <a href="{{ route('donations.show', $donation) }}" class="text-lg font-semibold text-blue-600 hover:underline">
                            {{ $donation->type }}
                        </a>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="text-sm text-gray-600">Qty: {{ $donation->quantity }}</span>
                            <span class="text-sm text-gray-600">Value: ₱{{ number_format($donation->value, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $donation->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="ml-4 px-3 py-1 text-sm rounded-full
                        @if($donation->status === 'Pending') bg-yellow-100 text-yellow-800
                        @elseif($donation->status === 'Verified') bg-blue-100 text-blue-800
                        @else bg-green-100 text-green-800 @endif">
                        {{ $donation->status }}
                    </span>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <p>No donations yet. Make your first donation to help the community!</p>
            </div>
        @endforelse
        
        @if($recentDonations->count() > 0)
            <div class="p-4 text-center">
                <a href="{{ route('donations.index') }}" class="text-blue-600 hover:underline">View All Donations →</a>
            </div>
        @endif
    </div>

    {{-- Distribution Transparency Ledger --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Distribution Transparency</h2>
            <p class="text-sm text-gray-600">See how your donations are being used</p>
        </div>
        
        @forelse($distributionLogs as $log)
            <div class="p-6 border-b">
                <div class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Distributed to: {{ $log->distributed_to }}</p>
                        <p class="text-sm text-gray-600 mt-1">Quantity: {{ $log->quantity_distributed }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $log->created_at->format('M d, Y h:i A') }}</p>
                        @if($log->remarks)
                            <p class="text-sm text-gray-600 mt-2 italic">"{{ $log->remarks }}"</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <p>No distribution logs yet.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection