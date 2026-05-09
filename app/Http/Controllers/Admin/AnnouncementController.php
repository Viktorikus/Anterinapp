<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('admin')->latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string|max:3000',
            'type'       => 'required|in:closure,event,route_change,repair,info',
            'is_active'  => 'boolean',
            'starts_at'  => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        Announcement::create([
            'admin_id'   => auth()->id(),
            'title'      => $request->title,
            'content'    => $request->content,
            'type'       => $request->type,
            'is_active'  => $request->boolean('is_active', true),
            'starts_at'  => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat!');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string|max:3000',
            'type'       => 'required|in:closure,event,route_change,repair,info',
            'is_active'  => 'boolean',
            'starts_at'  => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        $announcement->update([
            'title'      => $request->title,
            'content'    => $request->content,
            'type'       => $request->type,
            'is_active'  => $request->boolean('is_active'),
            'starts_at'  => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }
}
