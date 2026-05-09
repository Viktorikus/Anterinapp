<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Report;
use App\Models\TripHistory;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'       => User::where('role', 'user')->count(),
            'total_vehicles'    => Vehicle::count(),
            'active_vehicles'   => Vehicle::where('is_active', true)->count(),
            'total_reports'     => Report::count(),
            'today_reports'     => Report::whereDate('created_at', today())->count(),
            'active_reports'    => Report::where('status', 'active')->count(),
            'total_bookmarks'   => \App\Models\Bookmark::count(),
            'active_announcements' => Announcement::active()->count(),
        ];

        // Reports per day (last 7 days) for bar chart
        $reportsPerDay = [];
        $reportsLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $reportsLabels[] = $date->format('d M');
            $reportsPerDay[] = Report::whereDate('created_at', $date)->count();
        }

        // Report type distribution for pie chart
        $reportTypes = Report::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn($r) => [$r->type => $r->total]);

        // Top congested locations
        $topLocations = Report::where('type', 'macet')
            ->select('location_name', DB::raw('count(*) as total'))
            ->groupBy('location_name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // User activity per day (last 7 days)
        $userActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $userActivity[] = TripHistory::whereDate('created_at', $date)->count();
        }

        $recentReports = Report::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'stats', 'reportsPerDay', 'reportsLabels',
            'reportTypes', 'topLocations', 'userActivity', 'recentReports'
        ));
    }
}
