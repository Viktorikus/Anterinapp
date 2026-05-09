<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::active()->with('admin')->latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
    }
}
