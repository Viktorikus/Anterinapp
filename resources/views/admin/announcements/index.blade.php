@extends('layouts.app')
@section('title', 'Kelola Pengumuman')
@section('page-title', 'Kelola Pengumuman')
@section('topbar-actions')
  <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">➕ Buat Pengumuman</a>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title">📢 Semua Pengumuman</span>
    <span class="badge badge-teal">{{ $announcements->total() }}</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Tipe</th><th>Judul</th><th>Status</th><th>Berlaku Sampai</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($announcements as $a)
        <tr>
          <td>{{ $a->type_icon }} {{ $a->type_label }}</td>
          <td style="font-weight:600;color:#E6F1FF">{{ Str::limit($a->title, 50) }}</td>
          <td>
            <span class="badge {{ $a->is_active ? 'badge-teal' : 'badge-gray' }}">
              {{ $a->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </td>
          <td style="font-size:12px;color:#8892B0">
            {{ $a->expires_at ? $a->expires_at->format('d M Y') : '—' }}
          </td>
          <td style="font-size:12px;color:#8892B0">{{ $a->created_at->diffForHumans() }}</td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.announcements.edit', $a) }}" class="btn btn-secondary btn-sm">✏️</a>
              <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="padding:16px 22px">{{ $announcements->links() }}</div>
</div>
@endsection
