@extends('layouts.app')
@section('title', 'Cari Rute')
@section('page-title', 'Cari Rute Alternatif')

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div>
    <div class="card mb-4">
      <div class="card-header"><span class="card-title">🔄 Pencarian Rute</span></div>
      <div class="card-body">
        {{-- Bookmarks Quick-Fill --}}
        @if($bookmarks->isNotEmpty())
        <div style="margin-bottom:16px">
          <div style="font-size:12px;color:#8892B0;margin-bottom:8px;font-weight:600">⭐ Rute Favorit (klik untuk isi otomatis)</div>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            @foreach($bookmarks->take(5) as $bm)
            <button type="button" onclick="fillFromBookmark({{ $bm->origin_lat }},{{ $bm->origin_lng }},'{{ addslashes($bm->origin_name) }}',{{ $bm->destination_lat }},{{ $bm->destination_lng }},'{{ addslashes($bm->destination_name) }}')"
              class="btn btn-secondary btn-sm">{{ $bm->name }}</button>
            @endforeach
          </div>
        </div>
        @endif

        <form method="GET" action="{{ route('route-finder.index') }}" id="route-form">
          <div class="form-group">
            <label class="form-label">📍 Titik Asal</label>
            <input type="text" name="origin_name" id="origin-name" class="form-control"
              value="{{ $request->origin_name }}" placeholder="Nama lokasi asal" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px">
              <input type="number" name="origin_lat" id="origin-lat" class="form-control" step="any" value="{{ $request->origin_lat }}" placeholder="Lat" required>
              <input type="number" name="origin_lng" id="origin-lng" class="form-control" step="any" value="{{ $request->origin_lng }}" placeholder="Lng" required>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">🎯 Tujuan</label>
            <input type="text" name="destination_name" id="dest-name" class="form-control"
              value="{{ $request->destination_name }}" placeholder="Nama lokasi tujuan" required>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:8px">
              <input type="number" name="destination_lat" id="dest-lat" class="form-control" step="any" value="{{ $request->destination_lat }}" placeholder="Lat" required>
              <input type="number" name="destination_lng" id="dest-lng" class="form-control" step="any" value="{{ $request->destination_lng }}" placeholder="Lng" required>
            </div>
          </div>
          <div style="font-size:12px;color:#8892B0;margin-bottom:12px">💡 Klik peta: pertama = Asal, kedua = Tujuan</div>
          <button type="submit" class="btn btn-primary w-full" style="justify-content:center">🔍 Cari Rute</button>
        </form>
      </div>
    </div>

    @if($result)
    <div class="card">
      <div class="card-header"><span class="card-title">📋 Hasil Rute</span></div>
      <div class="card-body">
        <div style="margin-bottom:16px;padding:14px;background:rgba(255,107,107,0.08);border:1px solid rgba(255,107,107,0.25);border-radius:8px">
          <div style="font-weight:700;color:#E6F1FF;margin-bottom:6px">🔴 Rute Utama</div>
          <div style="font-size:13px;color:#CCD6F6">{{ $result['origin']['name'] }} → {{ $result['destination']['name'] }}</div>
          <div style="font-size:12px;color:#8892B0;margin-top:4px">⏱ Estimasi: {{ $result['estimated_primary'] }}</div>
        </div>

        @if($result['has_alternative'])
        <div style="margin-bottom:16px;padding:14px;background:rgba(77,150,255,0.08);border:1px solid rgba(77,150,255,0.25);border-radius:8px">
          <div style="font-weight:700;color:#E6F1FF;margin-bottom:6px">🔵 Rute Alternatif (Direkomendasikan)</div>
          <div style="font-size:12px;color:#8892B0;margin-bottom:8px">⚠️ Ruas macet dihindari: {{ implode(', ', $result['avoided_roads']) }}</div>
          <div style="font-size:13px;color:#CCD6F6">Lewat: {{ implode(', ', $result['alternative_roads']) }}</div>
          <div style="font-size:12px;color:#8892B0;margin-top:4px">⏱ Estimasi: {{ $result['estimated_alt'] }}</div>
        </div>
        @else
        <div style="padding:14px;background:rgba(107,203,119,0.08);border:1px solid rgba(107,203,119,0.25);border-radius:8px">
          <div style="font-weight:700;color:#6BCB77">✅ Jalur Lancar!</div>
          <div style="font-size:13px;color:#8892B0;margin-top:4px">Tidak ada laporan kemacetan signifikan di rute ini</div>
        </div>
        @endif
      </div>
    </div>
    @endif
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">🗺️ Peta Rute</span></div>
    <div class="map-container" style="border-radius:0 0 12px 12px"><div id="route-map" style="height:480px"></div></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('route-map').setView([-6.9175, 107.6191], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
let clickCount = 0, mO = null, mD = null;

@if($result)
const origin = [{{ $result['origin']['lat'] }}, {{ $result['origin']['lng'] }}];
const dest = [{{ $result['destination']['lat'] }}, {{ $result['destination']['lng'] }}];
const iconO = L.divIcon({html:'<div style="background:#00C9A7;border:3px solid #fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:12px">📍</div>',iconSize:[26,26],iconAnchor:[13,13],className:''});
const iconD = L.divIcon({html:'<div style="background:#FF6B6B;border:3px solid #fff;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:12px">🎯</div>',iconSize:[26,26],iconAnchor:[13,13],className:''});
L.marker(origin, {icon:iconO}).addTo(map).bindPopup('Asal: {{ addslashes($result["origin"]["name"]) }}').openPopup();
L.marker(dest, {icon:iconD}).addTo(map).bindPopup('Tujuan: {{ addslashes($result["destination"]["name"]) }}');
L.polyline([origin, dest], {color:'#FF6B6B', weight:4, dashArray:'8 4', opacity:0.8}).addTo(map);
@if($result['has_alternative'])
@foreach($result['congested_segments'] as $seg)
const cIcon = L.divIcon({html:'<div style="background:#FFD93D;border:2px solid #fff;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:10px">⚠️</div>',iconSize:[20,20],iconAnchor:[10,10],className:''});
L.marker([{{ $seg['lat'] }}, {{ $seg['lng'] }}], {icon:cIcon}).addTo(map).bindPopup('Macet: {{ addslashes($seg["name"]) }}');
@endforeach
const altLine = L.polyline([origin, [{{ ($result['origin']['lat'] + $result['destination']['lat'])/2 }}, {{ ($result['origin']['lng'] + $result['destination']['lng'])/2 + 0.02 }}], dest], {color:'#4D96FF', weight:4, opacity:0.8}).addTo(map);
@endif
map.fitBounds([origin, dest], {padding:[30,30]});
@else
map.on('click', function(e) {
  const {lat,lng} = e.latlng;
  if (clickCount % 2 === 0) {
    document.getElementById('origin-lat').value = lat.toFixed(6);
    document.getElementById('origin-lng').value = lng.toFixed(6);
    if (mO) map.removeLayer(mO);
    mO = L.marker([lat,lng],{icon:L.divIcon({html:'<div style="background:#00C9A7;border:3px solid #fff;border-radius:50%;width:22px;height:22px"></div>',iconSize:[22,22],iconAnchor:[11,11],className:''})}).addTo(map).bindPopup('Asal').openPopup();
  } else {
    document.getElementById('dest-lat').value = lat.toFixed(6);
    document.getElementById('dest-lng').value = lng.toFixed(6);
    if (mD) map.removeLayer(mD);
    mD = L.marker([lat,lng],{icon:L.divIcon({html:'<div style="background:#FF6B6B;border:3px solid #fff;border-radius:50%;width:22px;height:22px"></div>',iconSize:[22,22],iconAnchor:[11,11],className:''})}).addTo(map).bindPopup('Tujuan').openPopup();
  }
  clickCount++;
});
@endif

function fillFromBookmark(oLat, oLng, oName, dLat, dLng, dName) {
  document.getElementById('origin-lat').value = oLat;
  document.getElementById('origin-lng').value = oLng;
  document.getElementById('origin-name').value = oName;
  document.getElementById('dest-lat').value = dLat;
  document.getElementById('dest-lng').value = dLng;
  document.getElementById('dest-name').value = dName;
}
</script>
@endpush
