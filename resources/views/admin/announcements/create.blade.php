@extends('layouts.app')
@section('title', 'Buat Pengumuman')
@section('page-title', 'Buat Pengumuman Baru')
@section('topbar-actions')
  <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection
@section('content')
<div style="max-width:700px;margin:0 auto">
<div class="card">
  <div class="card-header"><span class="card-title">📢 Detail Pengumuman</span></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.announcements.store') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Judul pengumuman">
        @error('title')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">Tipe *</label>
        <select name="type" class="form-control" required>
          <option value="info">ℹ️ Informasi</option>
          <option value="closure">🚫 Penutupan Jalan</option>
          <option value="event">🎉 Event Kota</option>
          <option value="route_change">🔄 Perubahan Rute</option>
          <option value="repair">🔧 Perbaikan Jalan</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Isi Pengumuman *</label>
        <textarea name="content" class="form-control" rows="5" required placeholder="Tuliskan isi pengumuman...">{{ old('content') }}</textarea>
        @error('content')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Tanggal Mulai</label>
          <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Berakhir</label>
          <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-control">
          <option value="1">Aktif</option>
          <option value="0">Nonaktif (Draft)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center">📢 Publikasikan</button>
    </form>
  </div>
</div>
</div>
@endsection
