@extends('layouts.app')
@section('title', 'Edit Pengumuman')
@section('page-title', 'Edit Pengumuman')
@section('topbar-actions')
  <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection
@section('content')
<div style="max-width:700px;margin:0 auto">
<div class="card">
  <div class="card-header"><span class="card-title">✏️ Edit Pengumuman</span></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
      @csrf @method('PUT')
      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">Tipe *</label>
        <select name="type" class="form-control" required>
          @foreach(['info'=>'ℹ️ Informasi','closure'=>'🚫 Penutupan Jalan','event'=>'🎉 Event Kota','route_change'=>'🔄 Perubahan Rute','repair'=>'🔧 Perbaikan Jalan'] as $val=>$label)
          <option value="{{ $val }}" {{ $announcement->type==$val?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Isi Pengumuman *</label>
        <textarea name="content" class="form-control" rows="5" required>{{ old('content', $announcement->content) }}</textarea>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Tanggal Mulai</label>
          <input type="datetime-local" name="starts_at" class="form-control"
            value="{{ old('starts_at', $announcement->starts_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Tanggal Berakhir</label>
          <input type="datetime-local" name="expires_at" class="form-control"
            value="{{ old('expires_at', $announcement->expires_at?->format('Y-m-d\TH:i')) }}">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-control">
          <option value="1" {{ $announcement->is_active?'selected':'' }}>Aktif</option>
          <option value="0" {{ !$announcement->is_active?'selected':'' }}>Nonaktif</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center">💾 Simpan Perubahan</button>
    </form>
  </div>
</div>
</div>
@endsection
