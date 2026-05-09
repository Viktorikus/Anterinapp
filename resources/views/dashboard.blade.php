@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('topbar-actions')
  <span class="live-badge"><span class="live-dot"></span>Live</span>
@endsection

@section('content')
{{-- Announcement Banners --}}
@if($announcements->isNotEmpty())
<div class="mb-6">
  @foreach($announcements as $a)
  <a href="{{ route('announcements.index') }}" style="text-decoration:none">
    <div class="announcement-banner" style="background:rgba({{$a->type=='closure'?'255,107,107':($a->type=='event'?'255,217,61':($a->type=='repair'?'77,150,255':'0,201,167'))}},0.08);border:1px solid rgba({{$a->type=='closure'?'255,107,107':($a->type=='event'?'255,217,61':($a->type=='repair'?'77,150,255':'0,201,167'))}},0.25)">
      <span style="font-size:22px">{{ $a->type_icon }}</span>
      <div>
        <div style="font-weight:700;color:#E6F1FF;font-size:14px">{{ $a->title }}</div>
        <div style="font-size:12px;color:#8892B0;margin-top:2px">{{ Str::limit($a->content, 100) }}</div>
      </div>
    </div>
  </a>
  @endforeach
</div>
@endif

{{-- Stats --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon teal">🚌</div>
    <div>
      <div class="stat-value">{{ $activeVehicles }}</div>
      <div class="stat-label">Kendaraan Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red">🚨</div>
    <div>
      <div class="stat-value">{{ $todayReports }}</div>
      <div class="stat-label">Laporan Hari Ini</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon yellow">📊</div>
    <div>
      <div class="stat-value">{{ $totalReports }}</div>
      <div class="stat-label">Total Laporan</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon blue">⭐</div>
    <div>
      <div class="stat-value">{{ auth()->user()->bookmarks()->count() }}</div>
      <div class="stat-label">Rute Favorit Saya</div>
    </div>
  </div>
</div>

{{-- Quick Actions --}}
<div class="grid-2 mb-6">
  <a href="{{ route('monitoring.index') }}" class="card" style="text-decoration:none;padding:24px;display:flex;align-items:center;gap:16px;transition:transform .25s,box-shadow .25s" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,.5)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
    <div style="font-size:36px">📍</div>
    <div>
      <div style="font-weight:700;color:#E6F1FF;font-size:16px">Monitoring Kendaraan</div>
      <div style="color:#8892B0;font-size:13px">Lihat posisi armada secara live</div>
    </div>
  </a>
  <a href="{{ route('reports.create') }}" class="card" style="text-decoration:none;padding:24px;display:flex;align-items:center;gap:16px;transition:transform .25s,box-shadow .25s" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
    <div style="font-size:36px">🚨</div>
    <div>
      <div style="font-weight:700;color:#E6F1FF;font-size:16px">Buat Laporan</div>
      <div style="color:#8892B0;font-size:13px">Laporkan kemacetan atau kecelakaan</div>
    </div>
  </a>
</div>

{{-- Recent Reports --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">🚨 Laporan Terbaru</span>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($activeReports as $report)
    <a href="{{ route('reports.show', $report) }}" style="text-decoration:none">
      <div style="padding:14px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background=''">
        <span style="font-size:22px">{{ $report->type_icon }}</span>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;color:#E6F1FF;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $report->title }}</div>
          <div style="font-size:11px;color:#8892B0;margin-top:2px">📍 {{ $report->location_name }} · {{ $report->created_at->diffForHumans() }}</div>
        </div>
        <div style="text-align:right;flex-shrink:0">
          <span class="badge {{ $report->type=='macet'?'badge-red':($report->type=='kecelakaan'?'badge-yellow':'badge-blue') }}">{{ $report->type_label }}</span>
          <div style="font-size:11px;color:#8892B0;margin-top:4px">{{ $report->votes->count() }} suara</div>
        </div>
      </div>
    </a>
    @empty
    <div class="empty-state" style="padding:32px">
      <div class="empty-state-icon">📭</div>
      <div class="empty-state-title">Tidak ada laporan aktif</div>
    </div>
    @endforelse
  </div>
</div>
@endsection
