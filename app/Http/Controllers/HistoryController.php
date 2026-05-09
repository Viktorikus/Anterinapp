<?php

namespace App\Http\Controllers;

use App\Models\TripHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = TripHistory::where('user_id', auth()->id())
            ->with('bookmark')
            ->latest()
            ->take(30)
            ->get();

        return view('history.index', compact('histories'));
    }
}
