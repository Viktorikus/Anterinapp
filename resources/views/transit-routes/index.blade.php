@extends('layouts.app')
@section('title', 'Rute & Jadwal')
@section('page-title', 'Rute & Jadwal Transportasi')

@section('content')
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:18px">
  @foreach($routes as $route)
  <a href="{{ route('transit-routes.show', $route) }}" class="route-card">
    <div class="route-badge" style="background:{{ $route->color }}">{{ $route->code }}</div>
    <div class="route-name">{{ $route->name }}</div>
    <div style="font-size:12px;color:#8892B0;margin-bottom:12px">{{ $route->description }}</div>
    <div class="route-meta">
      <span>🚉 {{ $route->stops->count() }} halte</span>
      <span>📅 {{ $route->schedules->count() }} jadwal</span>
      @if($route->distance_km)
        <span>📏 {{ $route->distance_km }} km</span>
      @endif
    </div>
    <div style="margin-top:12px;font-size:12px;color:#8892B0;display:flex;justify-content:space-between">
      <span>Dari: <b style="color:#CCD6F6">{{ $route->start_point }}</b></span>
      <span>→</span>
      <span>Ke: <b style="color:#CCD6F6">{{ $route->end_point }}</b></span>
    </div>
  </a>
  @endforeach
</div>
@endsection
