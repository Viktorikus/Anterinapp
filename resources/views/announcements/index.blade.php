@extends('layouts.app')
@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman Resmi')

@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title">📢 Informasi Resmi dari ANTERIN</span>
    <span class="badge badge-teal">{{ $announcements->total() }} pengumuman</span>
  </div>
  <div class="card-body" style="padding:0">
    @forelse($announcements as $a)
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;gap:16px;align-items:flex-start">
      <div style="width:46px;height:46px;border-radius:10px;background:rgba(0,201,167,0.1);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">{{ $a->type_icon }}</div>
      <div style="flex:1">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
          <span style="font-weight:700;font-size:15px;color:#E6F1FF">{{ $a->title }}</span>
          <span class="badge" style="background:rgba(0,0,0,0.2);color:{{ $a->type_badge_color }};border:1px solid {{ $a->type_badge_color }}40">{{ $a->type_label }}</span>
        </div>
        <p style="font-size:13px;color:#CCD6F6;line-height:1.8;margin-bottom:10px">{{ $a->content }}</p>
        <div style="font-size:11px;color:#8892B0;display:flex;gap:16px;flex-wrap:wrap">
          <span>📅 {{ $a->created_at->format('d M Y') }}</span>
          @if($a->expires_at)<span>⏳ Berlaku sampai: {{ $a->expires_at->format('d M Y') }}</span>@endif
          <span>✍️ {{ $a->admin->name }}</span>
        </div>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <div class="empty-state-icon">📢</div>
      <div class="empty-state-title">Tidak ada pengumuman aktif</div>
      <div class="empty-state-desc">Belum ada informasi resmi saat ini</div>
    </div>
    @endforelse
  </div>
</div>
<div style="display:flex;justify-content:center;margin-top:20px">{{ $announcements->links() }}</div>
@endsection
