{{-- resources/views/dashboards/official.blade.php --}}
@extends('layouts.app')

@section('title', 'Official Dashboard')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">{{ auth()->user()->isAdmin() ? 'Admin' : 'Official' }} Dashboard</h1>
    <p class="text-gray-600 mt-2">System Overview and Analytics</p>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm">Critical Reports</p>
                <p class="text-3xl font-bold">{{ $criticalReports }}</p>
                <p class="text-xs mt-2">Requires immediate attention</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">Pending Reports</p>
                <p class="text-3xl font-bold">{{ $pendingReports }}</p>
                <p class="text-xs mt-2">Out of {{ $totalReports }} total</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Pending Donations</p>
                <p class="text-3xl font-bold">{{ $pendingDonations }}</p>
                <p class="text-xs mt-2">Awaiting verification</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Donation Value</p>
                <p class="text-3xl font-bold">₱{{ number_format($totalDonationValue, 0) }}</p>
                <p class="text-xs mt-2">{{ $totalDonations }} donations</p>
            </div>
            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
    </div>
</div>

{{-- Quick Stats Row --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-gray-500 text-sm">Verified Reports</p>
        <p class="text-2xl font-bold text-blue-600">{{ $verifiedReports }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-gray-500 text-sm">Resolved Reports</p>
        <p class="text-2xl font-bold text-green-600">{{ $resolvedReports }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-gray-500 text-sm">Verified Donations</p>
        <p class="text-2xl font-bold text-blue-600">{{ $verifiedDonations }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-gray-500 text-sm">Distributed</p>
        <p class="text-2xl font-bold text-green-600">{{ $distributedDonations }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-gray-500 text-sm">Total Users</p>
        <p class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    {{-- Reports by Category Chart --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Reports by Category</h3>
        <canvas id="categoryChart"></canvas>
    </div>

    {{-- Reports by Urgency Chart --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Reports by Urgency Level</h3>
        <canvas id="urgencyChart"></canvas>
    </div>
</div>

{{-- Monthly Trends Chart --}}
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Monthly Trends (Last 6 Months)</h3>
    <canvas id="trendsChart"></canvas>
</div>

{{-- Action Items --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    {{-- Urgent Reports --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-red-50">
            <h2 class="text-xl font-bold text-red-800 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Urgent Reports Requiring Action
            </h2>
        </div>
        
        @forelse($urgentReports as $report)
            <div class="p-4 border-b hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <a href="{{ route('reports.show', $report) }}" class="font-semibold text-blue-600 hover:underline">
                            {{ $report->title }}
                        </a>
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($report->description, 80) }}</p>
                        <div class="flex items-center mt-2 space-x-2">
                            <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ $report->category }}</span>
                            <span class="text-xs px-2 py-1 rounded {{ $report->urgency_level === 'Critical' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $report->urgency_level }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('reports.show', $report) }}" class="ml-4 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        Review
                    </a>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>No urgent reports at the moment.</p>
            </div>
        @endforelse
    </div>

    {{-- Pending Donations --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-purple-50">
            <h2 class="text-xl font-bold text-purple-800 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pending Donations for Verification
            </h2>
        </div>
        
        @forelse($pendingDonationsList as $donation)
            <div class="p-4 border-b hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <a href="{{ route('donations.show', $donation) }}" class="font-semibold text-blue-600 hover:underline">
                            {{ $donation->type }}
                        </a>
                        <p class="text-sm text-gray-600 mt-1">
                            Donor: {{ $donation->donor->name }}
                        </p>
                        <div class="flex items-center mt-2 space-x-3">
                            <span class="text-xs text-gray-600">Qty: {{ $donation->quantity }}</span>
                            <span class="text-xs text-gray-600">Value: ₱{{ number_format($donation->value, 2) }}</span>
                            <span class="text-xs text-gray-500">{{ $donation->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('donations.show', $donation) }}" class="ml-4 bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                        Verify
                    </a>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>No pending donations to verify.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Export Options --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Export Data</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('reports.export') }}" class="flex items-center justify-center p-4 border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-semibold text-gray-800">Export Reports to CSV</span>
        </a>

        <a href="{{ route('donations.export') }}" class="flex items-center justify-center p-4 border-2 border-green-600 rounded-lg hover:bg-green-50 transition">
            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-semibold text-gray-800">Export Donations to CSV</span>
        </a>
    </div>
</div>

@push('scripts')
<script>
// Reports by Category Chart
const categoryData = {!! json_encode($reportsByCategory) !!};
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.category),
        datasets: [{
            data: categoryData.map(item => item.total),
            backgroundColor: [
                '#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', 
                '#EC4899', '#14B8A6', '#F97316'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Reports by Urgency Chart
const urgencyData = {!! json_encode($reportsByUrgency) !!};
const urgencyCtx = document.getElementById('urgencyChart').getContext('2d');
new Chart(urgencyCtx, {
    type: 'bar',
    data: {
        labels: urgencyData.map(item => item.urgency_level),
        datasets: [{
            label: 'Number of Reports',
            data: urgencyData.map(item => item.total),
            backgroundColor: ['#EF4444', '#F59E0B', '#FCD34D', '#60A5FA']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Monthly Trends Chart
const monthlyReportsData = {!! json_encode($monthlyReports) !!};
const monthlyDonationsData = {!! json_encode($monthlyDonations) !!};

const trendsCtx = document.getElementById('trendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: monthlyReportsData.map(item => item.month),
        datasets: [{
            label: 'Incident Reports',
            data: monthlyReportsData.map(item => item.total),
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Donation Value (₱)',
            data: monthlyDonationsData.map(item => item.total_value),
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});
</script>
@endpush
@endsection