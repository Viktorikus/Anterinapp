@extends('layouts.app')
@section('title', 'Riwayat Perjalanan')
@section('page-title', 'Riwayat Perjalanan')

@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title">🕐 Riwayat 30 Perjalanan Terakhir</span>
    <span class="badge badge-teal">{{ $histories->count() }} perjalanan</span>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($histories as $h)
    <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
      <div style="font-size:24px">🗺️</div>
      <div style="flex:1">
        <div style="font-weight:600;color:#E6F1FF;font-size:13px">
          {{ $h->origin_name }} → {{ $h->destination_name }}
        </div>
        <div style="font-size:11px;color:#8892B0;margin-top:3px">
          🕐 {{ $h->created_at->format('d M Y, H:i') }}
          @if($h->bookmark)
            · ⭐ via bookmark "{{ $h->bookmark->name }}"
          @endif
        </div>
      </div>
      <a href="{{ route('route-finder.index', ['origin_name'=>$h->origin_name,'origin_lat'=>$h->origin_lat,'origin_lng'=>$h->origin_lng,'destination_name'=>$h->destination_name,'destination_lat'=>$h->destination_lat,'destination_lng'=>$h->destination_lng]) }}"
        class="btn btn-secondary btn-sm">🔄 Ulangi</a>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-state-icon">🕐</div>
      <div class="empty-state-title">Belum ada riwayat perjalanan</div>
      <div class="empty-state-desc">Gunakan fitur pencarian rute untuk mulai</div>
      <a href="{{ route('route-finder.index') }}" class="btn btn-primary">🔄 Cari Rute</a>
    </div>
    @endforelse
  </div>
</div>
@endsection
