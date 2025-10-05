<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use App\Models\Donation;
use App\Models\DonationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Route to appropriate dashboard based on role
        if ($user->isAdmin() || $user->isOfficial()) {
            return $this->officialDashboard();
        } elseif ($user->isDonor()) {
            return $this->donorDashboard();
        } else {
            return $this->residentDashboard();
        }
    }
    
    private function residentDashboard()
    {
        $userId = Auth::id();
        
        $data = [
            'totalReports' => IncidentReport::where('user_id', $userId)->count(),
            'pendingReports' => IncidentReport::where('user_id', $userId)->where('status', 'Pending')->count(),
            'verifiedReports' => IncidentReport::where('user_id', $userId)->where('status', 'Verified')->count(),
            'resolvedReports' => IncidentReport::where('user_id', $userId)->where('status', 'Resolved')->count(),
            'recentReports' => IncidentReport::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
        
        return view('dashboards.resident', $data);
    }
    
    private function donorDashboard()
    {
        $userId = Auth::id();
        
        $data = [
            'totalDonations' => Donation::where('donor_id', $userId)->count(),
            'totalValue' => Donation::where('donor_id', $userId)->sum('value'),
            'pendingDonations' => Donation::where('donor_id', $userId)->where('status', 'Pending')->count(),
            'verifiedDonations' => Donation::where('donor_id', $userId)->where('status', 'Verified')->count(),
            'distributedDonations' => Donation::where('donor_id', $userId)->where('status', 'Distributed')->count(),
            'recentDonations' => Donation::where('donor_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'distributionLogs' => DonationLog::whereHas('donation', function($query) use ($userId) {
                $query->where('donor_id', $userId);
            })->orderBy('created_at', 'desc')->limit(10)->get(),
        ];
        
        return view('dashboards.donor', $data);
    }
    
    private function officialDashboard()
    {
        $data = [
            // Report statistics
            'totalReports' => IncidentReport::count(),
            'pendingReports' => IncidentReport::where('status', 'Pending')->count(),
            'verifiedReports' => IncidentReport::where('status', 'Verified')->count(),
            'resolvedReports' => IncidentReport::where('status', 'Resolved')->count(),
            'criticalReports' => IncidentReport::where('urgency_level', 'Critical')->where('status', 'Pending')->count(),
            
            // Donation statistics
            'totalDonations' => Donation::count(),
            'totalDonationValue' => Donation::sum('value'),
            'pendingDonations' => Donation::where('status', 'Pending')->count(),
            'verifiedDonations' => Donation::where('status', 'Verified')->count(),
            'distributedDonations' => Donation::where('status', 'Distributed')->count(),
            
            // User statistics
            'totalUsers' => User::count(),
            
            // Urgent items requiring action
            'urgentReports' => IncidentReport::where('status', 'Pending')
                ->whereIn('urgency_level', ['Critical', 'High'])
                ->orderBy('urgency_level', 'desc')
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get(),
            
            'pendingDonationsList' => Donation::where('status', 'Pending')
                ->with('donor')
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get(),
            
            // Charts data
            'reportsByCategory' => IncidentReport::select('category', DB::raw('count(*) as total'))
                ->groupBy('category')
                ->get(),
            
            'reportsByUrgency' => IncidentReport::select('urgency_level', DB::raw('count(*) as total'))
                ->groupBy('urgency_level')
                ->orderByRaw("FIELD(urgency_level, 'Critical', 'High', 'Medium', 'Low')")
                ->get(),
            
            'monthlyReports' => IncidentReport::select(
                    DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'),
                    DB::raw('count(*) as total')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get(),
            
            'monthlyDonations' => Donation::select(
                    DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'),
                    DB::raw('sum(value) as total_value')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy(DB::raw('MIN(created_at)'))
                ->get(),
        ];
        
        return view('dashboards.official', $data);
    }
    
    public function exportReports()
    {
        $reports = IncidentReport::with('user')->get();
        
        $filename = 'incident_reports_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($reports) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Category', 'Location', 'Urgency', 'Status', 'Reporter', 'Created At']);
            
            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->id,
                    $report->title,
                    $report->category,
                    $report->location,
                    $report->urgency_level,
                    $report->status,
                    $report->user->name,
                    $report->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function exportDonations()
    {
        $donations = Donation::with('donor')->get();
        
        $filename = 'donations_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($donations) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Type', 'Quantity', 'Value', 'Status', 'Donor', 'Destination', 'Created At']);
            
            foreach ($donations as $donation) {
                fputcsv($file, [
                    $donation->id,
                    $donation->type,
                    $donation->quantity,
                    $donation->value,
                    $donation->status,
                    $donation->donor->name,
                    $donation->destination ?? 'General',
                    $donation->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}