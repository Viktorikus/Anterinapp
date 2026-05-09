@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Admin')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
{{-- Stats --}}
<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon blue">👤</div><div><div class="stat-value">{{ $stats['total_users'] }}</div><div class="stat-label">Total User</div></div></div>
  <div class="stat-card"><div class="stat-icon teal">🚌</div><div><div class="stat-value">{{ $stats['active_vehicles'] }}</div><div class="stat-label">Kendaraan Aktif</div></div></div>
  <div class="stat-card"><div class="stat-icon red">🚨</div><div><div class="stat-value">{{ $stats['today_reports'] }}</div><div class="stat-label">Laporan Hari Ini</div></div></div>
  <div class="stat-card"><div class="stat-icon yellow">📊</div><div><div class="stat-value">{{ $stats['active_reports'] }}</div><div class="stat-label">Laporan Aktif</div></div></div>
  <div class="stat-card"><div class="stat-icon green">⭐</div><div><div class="stat-value">{{ $stats['total_bookmarks'] }}</div><div class="stat-label">Total Bookmark</div></div></div>
  <div class="stat-card"><div class="stat-icon blue">📢</div><div><div class="stat-value">{{ $stats['active_announcements'] }}</div><div class="stat-label">Pengumuman Aktif</div></div></div>
</div>

<div class="grid-2 mb-6">
  {{-- Bar Chart --}}
  <div class="card">
    <div class="card-header"><span class="card-title">📊 Laporan per Hari (7 Hari)</span></div>
    <div class="card-body"><canvas id="barChart" height="180"></canvas></div>
  </div>

  {{-- Pie Chart --}}
  <div class="card">
    <div class="card-header"><span class="card-title">🥧 Distribusi Tipe Laporan</span></div>
    <div class="card-body"><canvas id="pieChart" height="180"></canvas></div>
  </div>
</div>

<div class="grid-2 mb-6">
  {{-- Line Chart --}}
  <div class="card">
    <div class="card-header"><span class="card-title">📈 Aktivitas User (7 Hari)</span></div>
    <div class="card-body"><canvas id="lineChart" height="150"></canvas></div>
  </div>

  {{-- Top Locations --}}
  <div class="card">
    <div class="card-header"><span class="card-title">📍 Lokasi Kemacetan Terbanyak</span></div>
    <div class="card-body" style="padding:0">
      @forelse($topLocations as $i => $loc)
      <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
        <span style="font-size:18px;font-weight:800;color:var(--accent);width:24px">{{ $i+1 }}</span>
        <div style="flex:1;font-size:13px;color:#CCD6F6">{{ $loc->location_name ?? 'Tidak diketahui' }}</div>
        <span class="badge badge-red">{{ $loc->total }} laporan</span>
      </div>
      @empty
      <div style="padding:20px;text-align:center;color:#8892B0;font-size:13px">Belum ada data</div>
      @endforelse
    </div>
  </div>
</div>

{{-- Recent Reports Table --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">🚨 Laporan Terbaru</span>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Tipe</th><th>Judul</th><th>Lokasi</th><th>Pelapor</th><th>Status</th><th>Waktu</th></tr></thead>
      <tbody>
        @foreach($recentReports as $r)
        <tr>
          <td>{{ $r->type_icon }} {{ $r->type_label }}</td>
          <td><a href="{{ route('reports.show',$r) }}" style="color:#00C9A7;text-decoration:none">{{ Str::limit($r->title,40) }}</a></td>
          <td style="font-size:12px;color:#8892B0">{{ $r->location_name ?? '-' }}</td>
          <td style="font-size:12px">{{ $r->user->name }}</td>
          <td><span class="badge {{ $r->status=='active'?'badge-teal':($r->status=='expired'?'badge-gray':'badge-green') }}">{{ ucfirst($r->status) }}</span></td>
          <td style="font-size:12px;color:#8892B0">{{ $r->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
const chartDefaults = {
  color: '#8892B0',
  plugins: { legend: { labels: { color: '#8892B0', font: { family: 'Inter' } } } },
  scales: {
    x: { ticks: { color: '#8892B0' }, grid: { color: 'rgba(100,130,180,0.1)' } },
    y: { ticks: { color: '#8892B0' }, grid: { color: 'rgba(100,130,180,0.1)' }, beginAtZero: true }
  }
};

new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: @json($reportsLabels),
    datasets: [{
      label: 'Jumlah Laporan',
      data: @json($reportsPerDay),
      backgroundColor: 'rgba(0,201,167,0.6)',
      borderColor: '#00C9A7',
      borderWidth: 2,
      borderRadius: 6
    }]
  },
  options: { responsive: true, ...chartDefaults }
});

const typeData = @json($reportTypes);
new Chart(document.getElementById('pieChart'), {
  type: 'doughnut',
  data: {
    labels: ['Kemacetan', 'Kecelakaan', 'Jalan Rusak', 'Lainnya'],
    datasets: [{
      data: [typeData.macet||0, typeData.kecelakaan||0, typeData.jalan_rusak||0, typeData.lainnya||0],
      backgroundColor: ['#FF6B6B','#FFD93D','#4D96FF','#8892B0'],
      borderColor: '#112240', borderWidth: 3
    }]
  },
  options: { responsive: true, plugins: { legend: { labels: { color: '#8892B0' } } } }
});

new Chart(document.getElementById('lineChart'), {
  type: 'line',
  data: {
    labels: @json($reportsLabels),
    datasets: [{
      label: 'Perjalanan Dicari',
      data: @json($userActivity),
      borderColor: '#4D96FF',
      backgroundColor: 'rgba(77,150,255,0.15)',
      fill: true, tension: 0.4,
      pointBackgroundColor: '#4D96FF', pointRadius: 4
    }]
  },
  options: { responsive: true, ...chartDefaults }
});
</script>
@endpush
