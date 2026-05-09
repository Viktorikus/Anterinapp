<?php

namespace App\Http\Controllers;

use App\Models\TransitRoute;
use Illuminate\Http\Request;

class TransitRouteController extends Controller
{
    public function index()
    {
        $routes = TransitRoute::with(['stops', 'schedules'])
            ->where('is_active', true)
            ->get();
        return view('transit-routes.index', compact('routes'));
    }

    public function show(TransitRoute $transitRoute)
    {
        $transitRoute->load(['stops', 'schedules']);
        return view('transit-routes.show', compact('transitRoute'));
    }
}
