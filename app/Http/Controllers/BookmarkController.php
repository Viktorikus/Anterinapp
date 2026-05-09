<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\TripHistory;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = Bookmark::where('user_id', auth()->id())
            ->orderByDesc('use_count')
            ->get();
        return view('bookmarks.index', compact('bookmarks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'origin_name'      => 'required|string|max:255',
            'origin_lat'       => 'required|numeric',
            'origin_lng'       => 'required|numeric',
            'destination_name' => 'required|string|max:255',
            'destination_lat'  => 'required|numeric',
            'destination_lng'  => 'required|numeric',
        ]);

        Bookmark::create([
            'user_id'          => auth()->id(),
            'name'             => $request->name,
            'origin_name'      => $request->origin_name,
            'origin_lat'       => $request->origin_lat,
            'origin_lng'       => $request->origin_lng,
            'destination_name' => $request->destination_name,
            'destination_lat'  => $request->destination_lat,
            'destination_lng'  => $request->destination_lng,
        ]);

        return redirect()->route('bookmarks.index')
            ->with('success', 'Rute favorit berhasil disimpan!');
    }

    public function destroy(Bookmark $bookmark)
    {
        if ($bookmark->user_id !== auth()->id()) abort(403);
        $bookmark->delete();
        return back()->with('success', 'Bookmark dihapus.');
    }

    public function use(Bookmark $bookmark)
    {
        if ($bookmark->user_id !== auth()->id()) abort(403);

        $bookmark->increment('use_count');

        TripHistory::create([
            'user_id'          => auth()->id(),
            'bookmark_id'      => $bookmark->id,
            'origin_name'      => $bookmark->origin_name,
            'origin_lat'       => $bookmark->origin_lat,
            'origin_lng'       => $bookmark->origin_lng,
            'destination_name' => $bookmark->destination_name,
            'destination_lat'  => $bookmark->destination_lat,
            'destination_lng'  => $bookmark->destination_lng,
        ]);

        return redirect()->route('route-finder.index', [
            'origin_name'      => $bookmark->origin_name,
            'origin_lat'       => $bookmark->origin_lat,
            'origin_lng'       => $bookmark->origin_lng,
            'destination_name' => $bookmark->destination_name,
            'destination_lat'  => $bookmark->destination_lat,
            'destination_lng'  => $bookmark->destination_lng,
        ]);
    }
}
