<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Report;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = Announcement::active()->latest()->take(3)->get();
        $activeReports = Report::active()->with('user', 'votes')->latest()->take(5)->get();
        $activeVehicles = Vehicle::where('is_active', true)->count();
        $todayReports = Report::whereDate('created_at', today())->count();
        $totalReports = Report::count();

        return view('dashboard', compact(
            'announcements', 'activeReports', 'activeVehicles',
            'todayReports', 'totalReports'
        ));
    }
}
