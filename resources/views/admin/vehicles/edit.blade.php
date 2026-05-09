@extends('layouts.app')
@section('title', 'Edit Kendaraan')
@section('page-title', 'Edit Kendaraan')
@section('topbar-actions')
  <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><span class="card-title">✏️ Edit {{ $vehicle->name }}</span></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}">
        @csrf @method('PUT')
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Nama Kendaraan *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $vehicle->name) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Kode Trayek *</label>
            <input type="text" name="trayek_code" class="form-control" value="{{ old('trayek_code', $vehicle->trayek_code) }}" required>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Nama Trayek *</label>
            <input type="text" name="trayek_name" class="form-control" value="{{ old('trayek_name', $vehicle->trayek_name) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tipe *</label>
            <select name="type" class="form-control" required>
              @foreach(['angkot','bus','kereta','transjakarta'] as $t)
              <option value="{{ $t }}" {{ $vehicle->type==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-control" required>
              <option value="berangkat" {{ $vehicle->status=='berangkat'?'selected':'' }}>Berangkat</option>
              <option value="berhenti" {{ $vehicle->status=='berhenti'?'selected':'' }}>Berhenti</option>
              <option value="menuju_halte" {{ $vehicle->status=='menuju_halte'?'selected':'' }}>Menuju Halte</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kapasitas</label>
            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $vehicle->capacity) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Plat Nomor</label>
            <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Nama Pengemudi</label>
            <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name', $vehicle->driver_name) }}">
          </div>
          <div class="form-group">
            <label class="form-label">ETA</label>
            <input type="text" name="estimated_arrival" class="form-control" value="{{ old('estimated_arrival', $vehicle->latestPosition?->estimated_arrival) }}" placeholder="5 menit">
          </div>
          <div class="form-group">
            <label class="form-label">Aktif</label>
            <select name="is_active" class="form-control">
              <option value="1" {{ $vehicle->is_active?'selected':'' }}>Ya</option>
              <option value="0" {{ !$vehicle->is_active?'selected':'' }}>Tidak</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Latitude Baru</label>
            <input type="number" name="latitude" id="lat-in" class="form-control" step="any"
              value="{{ $vehicle->latestPosition?->latitude }}">
          </div>
          <div class="form-group">
            <label class="form-label">Longitude Baru</label>
            <input type="number" name="longitude" id="lng-in" class="form-control" step="any"
              value="{{ $vehicle->latestPosition?->longitude }}">
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center">💾 Simpan Perubahan</button>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">📍 Update Posisi Kendaraan</span></div>
    <div class="map-container" style="border-radius:0 0 12px 12px"><div id="vehicle-map" style="height:420px"></div></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const initLat = {{ $vehicle->latestPosition?->latitude ?? -6.9175 }};
const initLng = {{ $vehicle->latestPosition?->longitude ?? 107.6191 }};
const map = L.map('vehicle-map').setView([initLat, initLng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
let marker = L.marker([initLat, initLng]).addTo(map).bindPopup('Posisi saat ini').openPopup();
map.on('click', function(e) {
  const {lat,lng} = e.latlng;
  document.getElementById('lat-in').value = lat.toFixed(6);
  document.getElementById('lng-in').value = lng.toFixed(6);
  marker.setLatLng([lat,lng]).setPopupContent(`Posisi baru: ${lat.toFixed(5)}, ${lng.toFixed(5)}`).openPopup();
});
</script>
@endpush
