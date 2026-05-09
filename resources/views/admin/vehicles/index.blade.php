@extends('layouts.app')
@section('title', 'Kelola Kendaraan')
@section('page-title', 'Kelola Kendaraan')
@section('topbar-actions')
  <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm">➕ Tambah Kendaraan</a>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title">🚌 Daftar Semua Kendaraan</span>
    <span class="badge badge-teal">{{ $vehicles->total() }} kendaraan</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Kendaraan</th><th>Trayek</th><th>Tipe</th><th>Status</th><th>Pengemudi</th><th>Posisi Terakhir</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($vehicles as $v)
        <tr>
          <td>
            <div style="font-weight:600;color:#E6F1FF">{{ $v->name }}</div>
            <div style="font-size:11px;color:#8892B0">{{ $v->plate_number }}</div>
          </td>
          <td><span class="badge badge-teal">{{ $v->trayek_code }}</span><br><span style="font-size:11px;color:#8892B0">{{ Str::limit($v->trayek_name,30) }}</span></td>
          <td style="text-transform:capitalize">{{ $v->type }}</td>
          <td><span class="badge {{ $v->status=='berangkat'?'badge-teal':($v->status=='berhenti'?'badge-red':'badge-yellow') }}">{{ $v->status_label }}</span></td>
          <td style="font-size:12px">{{ $v->driver_name ?? '-' }}</td>
          <td style="font-size:11px;color:#8892B0">
            @if($v->latestPosition)
              {{ $v->latestPosition->latitude }}, {{ $v->latestPosition->longitude }}<br>
              {{ $v->latestPosition->updated_at->diffForHumans() }}
            @else
              Belum ada posisi
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.vehicles.edit', $v) }}" class="btn btn-secondary btn-sm">✏️</a>
              <form method="POST" action="{{ route('admin.vehicles.destroy', $v) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
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
  <div style="padding:16px 22px">{{ $vehicles->links() }}</div>
</div>
@endsection
