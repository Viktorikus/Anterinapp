@extends('layouts.app')
@section('title', 'Tambah Kendaraan')
@section('page-title', 'Tambah Kendaraan Baru')
@section('topbar-actions')
  <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div class="card">
    <div class="card-header"><span class="card-title">🚌 Data Kendaraan</span></div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.vehicles.store') }}">
        @csrf
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Nama Kendaraan *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Angkot A1-001">
            @error('name')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Kode Trayek *</label>
            <input type="text" name="trayek_code" class="form-control" value="{{ old('trayek_code') }}" required placeholder="A1">
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Nama Trayek *</label>
            <input type="text" name="trayek_name" class="form-control" value="{{ old('trayek_name') }}" required placeholder="Cicaheum - Cibeureum">
          </div>
          <div class="form-group">
            <label class="form-label">Tipe *</label>
            <select name="type" class="form-control" required>
              <option value="angkot">Angkot</option>
              <option value="bus">Bus</option>
              <option value="kereta">Kereta</option>
              <option value="transjakarta">Transjakarta</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Kapasitas *</label>
            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', 20) }}" required min="1">
          </div>
          <div class="form-group">
            <label class="form-label">Plat Nomor</label>
            <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number') }}" placeholder="D 1234 AB">
          </div>
          <div class="form-group">
            <label class="form-label">Nama Pengemudi</label>
            <input type="text" name="driver_name" class="form-control" value="{{ old('driver_name') }}">
          </div>
          <div class="form-group">
            <label class="form-label">Latitude Awal</label>
            <input type="number" name="latitude" id="lat-in" class="form-control" step="any" value="{{ old('latitude') }}" placeholder="-6.9175">
          </div>
          <div class="form-group">
            <label class="form-label">Longitude Awal</label>
            <input type="number" name="longitude" id="lng-in" class="form-control" step="any" value="{{ old('longitude') }}" placeholder="107.6191">
          </div>
        </div>
        <div style="font-size:12px;color:#8892B0;margin-bottom:16px">💡 Klik peta untuk isi koordinat awal kendaraan</div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center">➕ Tambah Kendaraan</button>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">📍 Pilih Posisi Awal</span></div>
    <div class="map-container" style="border-radius:0 0 12px 12px"><div id="vehicle-map" style="height:420px"></div></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('vehicle-map').setView([-6.9175, 107.6191], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
let marker = null;
map.on('click', function(e) {
  const {lat,lng} = e.latlng;
  document.getElementById('lat-in').value = lat.toFixed(6);
  document.getElementById('lng-in').value = lng.toFixed(6);
  if (marker) map.removeLayer(marker);
  marker = L.marker([lat,lng]).addTo(map).bindPopup(`${lat.toFixed(5)}, ${lng.toFixed(5)}`).openPopup();
});
</script>
@endpush
