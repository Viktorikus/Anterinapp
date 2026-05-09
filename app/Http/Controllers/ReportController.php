<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Auto-expire old reports
        Report::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $query = Report::with(['user', 'votes'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when(!$request->status, fn($q) => $q->where('status', 'active'));

        $reports = $query->withCount([
            'votes as macet_count'  => fn($q) => $q->where('vote_type', 'macet'),
            'votes as padat_count'  => fn($q) => $q->where('vote_type', 'padat'),
            'votes as lancar_count' => fn($q) => $q->where('vote_type', 'lancar'),
        ])->orderByDesc('macet_count')->latest()->paginate(12);

        $allReports = Report::where('status', 'active')->get(['id', 'type', 'title', 'latitude', 'longitude', 'location_name']);

        return view('reports.index', compact('reports', 'allReports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:macet,kecelakaan,jalan_rusak,lainnya',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string|max:2000',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'location_name' => 'nullable|string|max:255',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', config('filesystems.default'));
        }

        Report::create([
            'user_id'       => auth()->id(),
            'type'          => $request->type,
            'title'         => $request->title,
            'description'   => $request->description,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'location_name' => $request->location_name,
            'photo_path'    => $photoPath,
            'status'        => 'active',
            'expires_at'    => now()->addHours(6),
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dibuat! Terima kasih atas kontribusi Anda.');
    }

    public function show(Report $report)
    {
        $report->load(['user', 'votes.user']);
        $report->checkAndExpire();

        $userVote = auth()->check()
            ? $report->votes->firstWhere('user_id', auth()->id())
            : null;

        $macetCount  = $report->votes->where('vote_type', 'macet')->count();
        $padatCount  = $report->votes->where('vote_type', 'padat')->count();
        $lancarCount = $report->votes->where('vote_type', 'lancar')->count();

        return view('reports.show', compact('report', 'userVote', 'macetCount', 'padatCount', 'lancarCount'));
    }

    public function vote(Request $request, Report $report)
    {
        $request->validate([
            'vote_type' => 'required|in:lancar,padat,macet',
        ]);

        if ($report->status !== 'active') {
            return back()->with('error', 'Laporan ini sudah tidak aktif.');
        }

        ReportVote::updateOrCreate(
            ['report_id' => $report->id, 'user_id' => auth()->id()],
            ['vote_type' => $request->vote_type]
        );

        return back()->with('success', 'Suara Anda telah dicatat!');
    }

    public function destroy(Report $report)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user || ($user->id !== $report->user_id && !$user->isAdmin())) {
            abort(403);
        }
        if ($report->photo_path) {
            Storage::disk(config('filesystems.default'))->delete($report->photo_path);
        }
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Laporan dihapus.');
    }
}
