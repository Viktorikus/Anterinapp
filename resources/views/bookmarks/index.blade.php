@extends('layouts.app')
@section('title', 'Rute Favorit')
@section('page-title', 'Rute Favorit')

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div>
    <div class="card mb-6">
      <div class="card-header"><span class="card-title">➕ Simpan Rute Favorit</span></div>
      <div class="card-body">
        <form method="POST" action="{{ route('bookmarks.store') }}">
          @csrf
          <div class="form-group">
            <label class="form-label">Nama Rute *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
              placeholder="Contoh: Rumah → Kampus" required>
            @error('name')<div class="error-msg">{{ $message }}</div>@enderror
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
              <label class="form-label">Nama Asal *</label>
              <input type="text" name="origin_name" class="form-control" value="{{ old('origin_name') }}"
                placeholder="Contoh: Kos Dago" required>
            </div>
            <div class="form-group">
              <label class="form-label">Nama Tujuan *</label>
              <input type="text" name="destination_name" class="form-control" value="{{ old('destination_name') }}"
                placeholder="Contoh: Kampus ITB" required>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
              <label class="form-label">Lat Asal</label>
              <input type="number" name="origin_lat" id="o-lat" class="form-control" step="any" value="{{ old('origin_lat') }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Lng Asal</label>
              <input type="number" name="origin_lng" id="o-lng" class="form-control" step="any" value="{{ old('origin_lng') }}" required>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
              <label class="form-label">Lat Tujuan</label>
              <input type="number" name="destination_lat" id="d-lat" class="form-control" step="any" value="{{ old('destination_lat') }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Lng Tujuan</label>
              <input type="number" name="destination_lng" id="d-lng" class="form-control" step="any" value="{{ old('destination_lng') }}" required>
            </div>
          </div>
          <div style="font-size:12px;color:#8892B0;margin-bottom:12px">💡 Klik peta untuk mengisi koordinat (klik 1x = Asal, klik 2x = Tujuan)</div>
          <button type="submit" class="btn btn-primary w-full" style="justify-content:center">⭐ Simpan Favorit</button>
        </form>
      </div>
    </div>
  </div>

  <div>
    <div class="card mb-6">
      <div class="card-header">
        <span class="card-title">📍 Pilih Koordinat di Peta</span>
      </div>
      <div class="map-container" style="border-radius:0 0 12px 12px">
        <div id="bookmark-map" style="height:360px"></div>
      </div>
      <div style="padding:12px 16px;font-size:12px;color:#8892B0" id="click-info">Klik pertama = Asal (hijau) · Klik kedua = Tujuan (merah)</div>
    </div>
  </div>
</div>

{{-- Bookmarks List --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">⭐ Rute Favorit Saya</span>
    <span class="badge badge-teal">{{ $bookmarks->count() }} rute</span>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($bookmarks as $bm)
    <div class="bookmark-card" style="margin:0;border-radius:0;border:none;border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
        <div style="flex:1">
          <div style="font-weight:700;font-size:15px;color:#E6F1FF;margin-bottom:6px">{{ $bm->name }}</div>
          <div class="bookmark-route">
            <span style="color:#00C9A7">📍 {{ $bm->origin_name }}</span>
            <span style="color:#8892B0">→</span>
            <span style="color:#FF6B6B">🎯 {{ $bm->destination_name }}</span>
          </div>
          <div style="font-size:11px;color:#8892B0;margin-top:6px">Digunakan {{ $bm->use_count }}x · Ditambah {{ $bm->created_at->diffForHumans() }}</div>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0">
          <form method="POST" action="{{ route('bookmarks.use', $bm) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">🔄 Gunakan</button>
          </form>
          <form method="POST" action="{{ route('bookmarks.destroy', $bm) }}" onsubmit="return confirm('Hapus bookmark ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-state-icon">⭐</div>
      <div class="empty-state-title">Belum ada rute favorit</div>
      <div class="empty-state-desc">Simpan rute yang sering Anda gunakan untuk akses cepat</div>
    </div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('bookmark-map').setView([-6.9175, 107.6191], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
let clickCount = 0, mO = null, mD = null;
map.on('click', function(e) {
  const {lat, lng} = e.latlng;
  if (clickCount % 2 === 0) {
    document.getElementById('o-lat').value = lat.toFixed(6);
    document.getElementById('o-lng').value = lng.toFixed(6);
    if (mO) map.removeLayer(mO);
    mO = L.marker([lat,lng],{icon:L.divIcon({html:'<div style="background:#00C9A7;border:3px solid #fff;border-radius:50%;width:22px;height:22px"></div>',iconSize:[22,22],iconAnchor:[11,11],className:''})}).addTo(map).bindPopup('Asal').openPopup();
    document.getElementById('click-info').textContent = `Asal dipilih: ${lat.toFixed(5)}, ${lng.toFixed(5)} · Klik lagi untuk Tujuan`;
  } else {
    document.getElementById('d-lat').value = lat.toFixed(6);
    document.getElementById('d-lng').value = lng.toFixed(6);
    if (mD) map.removeLayer(mD);
    mD = L.marker([lat,lng],{icon:L.divIcon({html:'<div style="background:#FF6B6B;border:3px solid #fff;border-radius:50%;width:22px;height:22px"></div>',iconSize:[22,22],iconAnchor:[11,11],className:''})}).addTo(map).bindPopup('Tujuan').openPopup();
    document.getElementById('click-info').textContent = `Tujuan dipilih: ${lat.toFixed(5)}, ${lng.toFixed(5)} · Klik lagi untuk reset Asal`;
  }
  clickCount++;
});
</script>
@endpush
