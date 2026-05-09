@extends('layouts.app')
@section('title', $transitRoute->name)
@section('page-title', $transitRoute->name)
@section('topbar-actions')
  <a href="{{ route('transit-routes.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div>
    <div class="card mb-4">
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
          <div class="route-badge" style="background:{{ $transitRoute->color }};width:52px;height:52px;font-size:16px">{{ $transitRoute->code }}</div>
          <div>
            <h2 style="font-weight:800;font-size:18px;color:#E6F1FF">{{ $transitRoute->name }}</h2>
            <div style="font-size:12px;color:#8892B0;margin-top:3px">{{ $transitRoute->description }}</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;text-align:center">
          <div style="padding:12px;background:rgba(0,201,167,0.08);border-radius:8px;border:1px solid rgba(0,201,167,0.2)">
            <div style="font-size:20px;font-weight:800;color:#00C9A7">{{ $transitRoute->stops->count() }}</div>
            <div style="font-size:11px;color:#8892B0">Halte</div>
          </div>
          <div style="padding:12px;background:rgba(77,150,255,0.08);border-radius:8px;border:1px solid rgba(77,150,255,0.2)">
            <div style="font-size:20px;font-weight:800;color:#4D96FF">{{ $transitRoute->schedules->count() }}</div>
            <div style="font-size:11px;color:#8892B0">Jadwal</div>
          </div>
          <div style="padding:12px;background:rgba(255,217,61,0.08);border-radius:8px;border:1px solid rgba(255,217,61,0.2)">
            <div style="font-size:20px;font-weight:800;color:#FFD93D">{{ $transitRoute->distance_km ?? '-' }}</div>
            <div style="font-size:11px;color:#8892B0">km</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Stops --}}
    <div class="card mb-4">
      <div class="card-header"><span class="card-title">🚉 Daftar Halte</span></div>
      <div class="card-body">
        <ul class="stop-list">
          @foreach($transitRoute->stops as $stop)
          <li class="stop-item">
            <div class="stop-dot">{{ $loop->iteration }}</div>
            <div class="stop-name">{{ $stop->name }}</div>
          </li>
          @endforeach
        </ul>
      </div>
    </div>

    {{-- Schedules --}}
    <div class="card">
      <div class="card-header"><span class="card-title">📅 Jadwal Keberangkatan</span></div>
      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Berangkat</th>
            <th>Tiba</th>
            <th>Hari</th>
            <th>Keterangan</th>
          </tr></thead>
          <tbody>
            @foreach($transitRoute->schedules as $sc)
            <tr>
              <td><b style="color:#00C9A7">{{ substr($sc->departure_time,0,5) }}</b></td>
              <td>{{ $sc->arrival_time ? substr($sc->arrival_time,0,5) : '-' }}</td>
              <td style="font-size:12px">{{ $sc->days_label }}</td>
              <td style="font-size:12px;color:#8892B0">{{ $sc->notes ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">🗺️ Peta Rute</span></div>
    <div class="map-container" style="border-radius:0 0 12px 12px">
      <div id="route-detail-map" style="height:500px"></div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const stops = @json($transitRoute->stops->map(fn($s) => ['name'=>$s->name,'lat'=>$s->latitude,'lng'=>$s->longitude]));
const color = '{{ $transitRoute->color }}';
const firstStop = stops[0];
const map = L.map('route-detail-map').setView([firstStop.lat, firstStop.lng], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);

const coords = stops.map(s => [s.lat, s.lng]);
L.polyline(coords, {color, weight:5, opacity:0.85}).addTo(map);

stops.forEach((s,i) => {
  const isFirst = i===0, isLast = i===stops.length-1;
  const icon = L.divIcon({
    html:`<div style="background:${isFirst||isLast?color:'#fff'};border:3px solid ${color};border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:${isFirst||isLast?'#fff':color}">${i+1}</div>`,
    iconSize:[20,20],iconAnchor:[10,10],className:''
  });
  L.marker([s.lat,s.lng],{icon}).addTo(map).bindPopup(`<b>${i+1}. ${s.name}</b>`);
});

if (coords.length > 1) map.fitBounds(coords, {padding:[20,20]});
</script>
@endpush
