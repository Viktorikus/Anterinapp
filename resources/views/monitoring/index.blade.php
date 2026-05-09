@extends('layouts.app')
@section('title', 'Monitoring Kendaraan')
@section('page-title', 'Monitoring Kendaraan')
@section('topbar-actions')
  <span class="live-badge"><span class="live-dot"></span>Auto-refresh 30s</span>
@endsection

@section('content')
<div class="grid-2" style="gap:20px;margin-bottom:20px">
  {{-- Map --}}
  <div style="grid-column:1/2" class="card" style="grid-column:span 1">
    <div class="card-header">
      <span class="card-title">📍 Peta Kendaraan</span>
      <span id="last-update" style="font-size:12px;color:#8892B0"></span>
    </div>
    <div class="map-container" style="border-radius:0 0 12px 12px">
      <div id="monitoring-map"></div>
    </div>
  </div>

  {{-- Vehicle List --}}
  <div style="grid-column:2/3">
    <div class="card" style="height:100%">
      <div class="card-header">
        <span class="card-title">🚌 Daftar Kendaraan</span>
        <span id="vehicle-count" class="badge badge-teal">{{ $vehicles->count() }} aktif</span>
      </div>
      <div id="vehicle-list" style="overflow-y:auto;max-height:440px">
        @foreach($vehicles as $v)
        <div class="vehicle-item" data-id="{{ $v->id }}" style="padding:14px 20px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .2s" onclick="focusVehicle({{ $v->id }})">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
            <span style="font-size:18px">{{ $v->type=='bus'?'🚌':($v->type=='kereta'?'🚆':'🚐') }}</span>
            <div style="flex:1">
              <div style="font-weight:700;font-size:13px;color:#E6F1FF">{{ $v->name }}</div>
              <div style="font-size:11px;color:#8892B0">{{ $v->trayek_code }} · {{ $v->trayek_name }}</div>
            </div>
            <span class="badge {{ $v->status=='berangkat'?'badge-teal':($v->status=='berhenti'?'badge-red':'badge-yellow') }}">
              {{ $v->status_label }}
            </span>
          </div>
          <div style="display:flex;gap:16px;font-size:11px;color:#8892B0">
            <span>👤 {{ $v->driver_name ?? 'N/A' }}</span>
            <span>🚗 {{ $v->plate_number ?? 'N/A' }}</span>
            @if($v->latestPosition)
              <span>⏱ {{ $v->latestPosition->estimated_arrival ?? '-' }}</span>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

@if(auth()->user()->isAdmin())
<div class="card">
  <div class="card-header">
    <span class="card-title">✏️ Update Posisi Kendaraan</span>
    <span style="font-size:12px;color:#8892B0">Klik peta untuk pilih lokasi</span>
  </div>
  <div class="card-body">
    <form id="update-pos-form" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;align-items:end">
      @csrf
      <div class="form-group" style="margin:0">
        <label class="form-label">Kendaraan</label>
        <select class="form-control" id="pos-vehicle-id" name="vehicle_id">
          @foreach($vehicles as $v)
          <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->trayek_code }})</option>
          @endforeach
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Status</label>
        <select class="form-control" name="status" id="pos-status">
          <option value="berangkat">Berangkat</option>
          <option value="berhenti">Berhenti</option>
          <option value="menuju_halte">Menuju Halte</option>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">ETA</label>
        <input type="text" class="form-control" name="estimated_arrival" id="pos-eta" placeholder="contoh: 5 menit">
      </div>
      <input type="hidden" name="latitude" id="pos-lat">
      <input type="hidden" name="longitude" id="pos-lng">
      <button type="submit" class="btn btn-primary">📍 Update Posisi</button>
    </form>
    <div id="pos-coords" style="font-size:12px;color:#8892B0;margin-top:10px">Klik peta untuk memilih koordinat lokasi kendaraan</div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const BANDUNG = [-6.9175, 107.6191];
const map = L.map('monitoring-map').setView(BANDUNG, 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap', maxZoom: 19
}).addTo(map);

const markers = {};
let adminMarker = null;

function statusColor(s) {
  return s==='berangkat' ? '#00C9A7' : s==='berhenti' ? '#FF6B6B' : '#FFD93D';
}
function makeIcon(v) {
  const color = statusColor(v.status);
  const emoji = v.type==='bus' ? '🚌' : v.type==='kereta' ? '🚆' : '🚐';
  return L.divIcon({
    html: `<div style="background:${color};border:3px solid #fff;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,.5)">${emoji}</div>`,
    iconSize: [36,36], iconAnchor: [18,18], className: ''
  });
}

function refreshPositions() {
  fetch('/api/vehicles/positions')
    .then(r => r.json())
    .then(vehicles => {
      vehicles.forEach(v => {
        if (!v.latitude || !v.longitude) return;
        const latlng = [v.latitude, v.longitude];
        const popup = `<div class="vehicle-popup">
          <h4>${v.name}</h4>
          <p><b>Trayek:</b> ${v.trayek_code} – ${v.trayek_name}</p>
          <p><b>Status:</b> ${v.status_label}</p>
          <p><b>Pengemudi:</b> ${v.driver_name||'N/A'}</p>
          <p><b>ETA:</b> ${v.estimated_arrival||'N/A'}</p>
          <p style="color:#888;font-size:11px">${v.updated_at||''}</p>
        </div>`;
        if (markers[v.id]) {
          markers[v.id].setLatLng(latlng).setIcon(makeIcon(v)).setPopupContent(popup);
        } else {
          markers[v.id] = L.marker(latlng, {icon: makeIcon(v)}).addTo(map).bindPopup(popup);
        }
      });
      document.getElementById('last-update').textContent = 'Diperbarui: ' + new Date().toLocaleTimeString('id-ID');
    });
}

function focusVehicle(id) {
  if (markers[id]) {
    map.flyTo(markers[id].getLatLng(), 16, {duration: 1});
    markers[id].openPopup();
  }
}

refreshPositions();
setInterval(refreshPositions, 30000);

@if(auth()->user()->isAdmin())
map.on('click', function(e) {
  const {lat, lng} = e.latlng;
  document.getElementById('pos-lat').value = lat.toFixed(6);
  document.getElementById('pos-lng').value = lng.toFixed(6);
  document.getElementById('pos-coords').textContent = `Koordinat dipilih: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
  if (adminMarker) map.removeLayer(adminMarker);
  adminMarker = L.marker([lat,lng], {
    icon: L.divIcon({html:'<div style="background:#fff;border:3px solid #00C9A7;border-radius:50%;width:20px;height:20px"></div>',iconSize:[20,20],iconAnchor:[10,10],className:''})
  }).addTo(map);
});

document.getElementById('update-pos-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const vId = document.getElementById('pos-vehicle-id').value;
  const data = {
    _token: document.querySelector('[name=_token]').value,
    latitude: document.getElementById('pos-lat').value,
    longitude: document.getElementById('pos-lng').value,
    status: document.getElementById('pos-status').value,
    estimated_arrival: document.getElementById('pos-eta').value
  };
  if (!data.latitude) { alert('Klik peta terlebih dahulu!'); return; }
  fetch(`/api/vehicles/${vId}/position`, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':data._token}, body: JSON.stringify(data)})
    .then(r => r.json())
    .then(res => { alert(res.message); refreshPositions(); })
    .catch(() => alert('Gagal update posisi'));
});
@endif
</script>
@endpush
