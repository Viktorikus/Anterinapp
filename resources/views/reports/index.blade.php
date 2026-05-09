@extends('layouts.app')
@section('title', 'Laporan Kondisi Jalan')
@section('page-title', 'Laporan Kondisi Jalan')
@section('topbar-actions')
  <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm">➕ Buat Laporan</a>
@endsection

@section('content')
{{-- Filter --}}
<div class="card mb-6">
  <div class="card-body" style="padding:16px 22px">
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <select name="type" class="form-control" style="width:auto">
        <option value="">Semua Tipe</option>
        <option value="macet" {{ request('type')=='macet'?'selected':'' }}>🚨 Kemacetan</option>
        <option value="kecelakaan" {{ request('type')=='kecelakaan'?'selected':'' }}>🚑 Kecelakaan</option>
        <option value="jalan_rusak" {{ request('type')=='jalan_rusak'?'selected':'' }}>⚠️ Jalan Rusak</option>
        <option value="lainnya" {{ request('type')=='lainnya'?'selected':'' }}>ℹ️ Lainnya</option>
      </select>
      <select name="status" class="form-control" style="width:auto">
        <option value="">Aktif Saja</option>
        <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Kedaluwarsa</option>
        <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">🔍 Filter</button>
      <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Reset</a>
    </form>
  </div>
</div>

{{-- Map --}}
<div class="card mb-6">
  <div class="card-header">
    <span class="card-title">🗺️ Peta Laporan Aktif</span>
    <span class="badge badge-red">{{ count($allReports) }} laporan</span>
  </div>
  <div class="map-container" style="border-radius:0 0 12px 12px"><div id="report-map"></div></div>
</div>

{{-- Report Grid --}}
@if($reports->isNotEmpty())
<div class="report-grid">
  @foreach($reports as $report)
  <a href="{{ route('reports.show', $report) }}" class="report-card">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
      <div class="report-type-icon" style="margin:0">{{ $report->type_icon }}</div>
      <span class="badge {{ $report->type=='macet'?'badge-red':($report->type=='kecelakaan'?'badge-yellow':'badge-blue') }}">{{ $report->type_label }}</span>
    </div>
    <div class="report-title">{{ $report->title }}</div>
    <div style="font-size:12px;color:#8892B0;margin-bottom:12px">{{ Str::limit($report->description, 80) }}</div>
    @if($report->photo_path)
    <img src="{{ $report->photo_url }}" alt="Foto" style="width:100%;height:140px;object-fit:cover;border-radius:8px;margin-bottom:12px">
    @endif
    <div class="report-meta">
      <span>📍 {{ $report->location_name ?? 'Bandung' }}</span>
      <span>🕐 {{ $report->created_at->diffForHumans() }}</span>
    </div>
    <div style="display:flex;gap:10px;margin-top:10px;font-size:12px">
      <span style="color:#FF6B6B">🚨 {{ $report->macet_count }}</span>
      <span style="color:#FFD93D">⚠️ {{ $report->padat_count }}</span>
      <span style="color:#6BCB77">✅ {{ $report->lancar_count }}</span>
    </div>
  </a>
  @endforeach
</div>
<div style="display:flex;justify-content:center;margin-top:24px">{{ $reports->links() }}</div>
@else
<div class="card"><div class="card-body">
  <div class="empty-state">
    <div class="empty-state-icon">📭</div>
    <div class="empty-state-title">Tidak ada laporan</div>
    <div class="empty-state-desc">Belum ada laporan yang sesuai filter</div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">➕ Buat Laporan Pertama</a>
  </div>
</div></div>
@endif
@endsection

@push('scripts')
<script>
const map = L.map('report-map').setView([-6.9175, 107.6191], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(map);
const reports = @json($allReports);
reports.forEach(r => {
  const colors = {macet:'#FF6B6B', kecelakaan:'#FFD93D', jalan_rusak:'#4D96FF', lainnya:'#8892B0'};
  const icon = L.divIcon({
    html: `<div style="background:${colors[r.type]||'#999'};border:3px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:13px;box-shadow:0 2px 6px rgba(0,0,0,.5)">${r.type==='macet'?'🚨':r.type==='kecelakaan'?'🚑':'⚠️'}</div>`,
    iconSize:[28,28],iconAnchor:[14,14],className:''
  });
  L.marker([r.latitude, r.longitude], {icon}).addTo(map)
    .bindPopup(`<b>${r.title}</b><br><small>${r.location_name||''}</small><br><a href="/reports/${r.id}" style="color:#00C9A7">Lihat Detail →</a>`);
});
</script>
@endpush
