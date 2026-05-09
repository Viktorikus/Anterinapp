@extends('layouts.app')
@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan Baru')

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div>
    <div class="card">
      <div class="card-header"><span class="card-title">📋 Detail Laporan</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" id="report-form">
          @csrf
          <div class="form-group">
            <label class="form-label">Tipe Kejadian *</label>
            <select name="type" class="form-control {{ $errors->has('type') ? 'input-error' : '' }}" required>
              <option value="">-- Pilih Tipe --</option>
              <option value="macet" {{ old('type')=='macet'?'selected':'' }}>🚨 Kemacetan</option>
              <option value="kecelakaan" {{ old('type')=='kecelakaan'?'selected':'' }}>🚑 Kecelakaan</option>
              <option value="jalan_rusak" {{ old('type')=='jalan_rusak'?'selected':'' }}>⚠️ Jalan Rusak</option>
              <option value="lainnya" {{ old('type')=='lainnya'?'selected':'' }}>ℹ️ Lainnya</option>
            </select>
            @error('type')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Judul Laporan *</label>
            <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'input-error' : '' }}"
              value="{{ old('title') }}" placeholder="Contoh: Kemacetan parah di Jl. Soekarno-Hatta" required>
            @error('title')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" name="location_name" class="form-control"
              value="{{ old('location_name') }}" id="location-name-input" placeholder="Contoh: Jl. Asia Afrika, Bandung">
          </div>
          <div class="form-group">
            <label class="form-label">Koordinat (klik peta) *</label>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
              <input type="number" name="latitude" id="lat-input" class="form-control" step="any"
                value="{{ old('latitude') }}" placeholder="Latitude" required readonly>
              <input type="number" name="longitude" id="lng-input" class="form-control" step="any"
                value="{{ old('longitude') }}" placeholder="Longitude" required readonly>
            </div>
            <div class="form-hint">Klik titik pada peta di sebelah kanan untuk mengisi koordinat otomatis</div>
            @error('latitude')<div class="error-msg">Koordinat wajib diisi (klik peta)</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Deskripsi *</label>
            <textarea name="description" class="form-control {{ $errors->has('description') ? 'input-error' : '' }}"
              rows="4" placeholder="Jelaskan situasi saat ini..." required>{{ old('description') }}</textarea>
            @error('description')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Foto (opsional)</label>
            <div class="upload-zone" id="upload-zone">
              <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" id="photo-input">
              <div class="upload-zone-icon">📷</div>
              <div class="upload-zone-text">Klik atau drag foto di sini<br><small>JPG, PNG, WEBP – maks 5MB</small></div>
              <img id="photo-preview" class="upload-preview" alt="Preview">
            </div>
            @error('photo')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary w-full" style="justify-content:center">
            🚨 Kirim Laporan
          </button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-header">
        <span class="card-title">📍 Pilih Lokasi di Peta</span>
        <span style="font-size:12px;color:#8892B0">Klik untuk pilih koordinat</span>
      </div>
      <div class="map-container" style="border-radius:0 0 12px 12px"><div id="report-map" style="height:400px"></div></div>
    </div>
    <div class="card mt-4">
      <div class="card-body" style="padding:16px 20px">
        <div style="font-size:13px;font-weight:600;color:#E6F1FF;margin-bottom:8px">ℹ️ Catatan</div>
        <ul style="font-size:12px;color:#8892B0;padding-left:16px;line-height:2">
          <li>Laporan aktif selama <b style="color:#00C9A7">6 jam</b></li>
          <li>Pengguna lain dapat melakukan voting terhadap laporan Anda</li>
          <li>Laporan yang tidak valid dapat dihapus oleh admin</li>
          <li>Foto dapat membantu validasi laporan</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('report-map').setView([-6.9175, 107.6191], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(map);
let marker = null;
map.on('click', function(e) {
  const {lat, lng} = e.latlng;
  document.getElementById('lat-input').value = lat.toFixed(6);
  document.getElementById('lng-input').value = lng.toFixed(6);
  if (marker) map.removeLayer(marker);
  marker = L.marker([lat,lng]).addTo(map).bindPopup(`Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`).openPopup();
});

document.getElementById('photo-input').addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    const prev = document.getElementById('photo-preview');
    prev.src = e.target.result;
    prev.style.display = 'block';
    document.querySelector('.upload-zone-icon').style.display = 'none';
    document.querySelector('.upload-zone-text').style.display = 'none';
  };
  reader.readAsDataURL(file);
});
</script>
@endpush
