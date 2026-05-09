<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'ANTERIN') – Smart City Transportation</title>
<meta name="description" content="ANTERIN – Smart City Transportation Monitoring System Bandung">
<link rel="stylesheet" href="{{ asset('css/anterin.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@stack('head')
</head>
<body>
<div class="app-layout">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">🚌</div>
      <div>
        <div class="logo-text">ANTERIN</div>
        <div class="logo-sub">Smart City Transportation</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Utama</div>
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="nav-icon">🏠</span><span class="nav-label">Dashboard</span>
      </a>
      <a href="{{ route('monitoring.index') }}" class="nav-item {{ request()->routeIs('monitoring*') ? 'active' : '' }}">
        <span class="nav-icon">📍</span><span class="nav-label">Monitoring Kendaraan</span>
      </a>

      <div class="nav-section-label">Komunitas</div>
      <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
        <span class="nav-icon">🚨</span><span class="nav-label">Laporan Kondisi</span>
      </a>
      <a href="{{ route('reports.create') }}" class="nav-item {{ request()->routeIs('reports.create') ? 'active' : '' }}">
        <span class="nav-icon">➕</span><span class="nav-label">Buat Laporan</span>
      </a>

      <div class="nav-section-label">Perjalanan</div>
      <a href="{{ route('route-finder.index') }}" class="nav-item {{ request()->routeIs('route-finder*') ? 'active' : '' }}">
        <span class="nav-icon">🔄</span><span class="nav-label">Cari Rute</span>
      </a>
      <a href="{{ route('bookmarks.index') }}" class="nav-item {{ request()->routeIs('bookmarks*') ? 'active' : '' }}">
        <span class="nav-icon">⭐</span><span class="nav-label">Rute Favorit</span>
      </a>
      <a href="{{ route('history.index') }}" class="nav-item {{ request()->routeIs('history*') ? 'active' : '' }}">
        <span class="nav-icon">🕐</span><span class="nav-label">Riwayat Perjalanan</span>
      </a>

      <div class="nav-section-label">Informasi</div>
      <a href="{{ route('transit-routes.index') }}" class="nav-item {{ request()->routeIs('transit-routes*') ? 'active' : '' }}">
        <span class="nav-icon">🗺️</span><span class="nav-label">Rute & Jadwal</span>
      </a>
      <a href="{{ route('announcements.index') }}" class="nav-item {{ request()->routeIs('announcements.index') ? 'active' : '' }}">
        <span class="nav-icon">📢</span><span class="nav-label">Pengumuman</span>
      </a>

      @if(auth()->check() && auth()->user()->isAdmin())
      <div class="nav-section-label">Admin</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">📊</span><span class="nav-label">Dashboard Admin</span>
      </a>
      <a href="{{ route('admin.vehicles.index') }}" class="nav-item {{ request()->routeIs('admin.vehicles*') ? 'active' : '' }}">
        <span class="nav-icon">🚌</span><span class="nav-label">Kelola Kendaraan</span>
      </a>
      <a href="{{ route('admin.announcements.index') }}" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
        <span class="nav-icon">📝</span><span class="nav-label">Kelola Pengumuman</span>
      </a>
      @endif
    </nav>

    <div class="sidebar-footer">
      <div class="user-info">
        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar">
        <div style="flex:1;min-width:0">
          <div class="user-name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</div>
          <div class="user-role">{{ auth()->user()->role }}</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
        @csrf
        <button type="submit" class="btn btn-secondary w-full" style="justify-content:center">
          🚪 Keluar
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main-content">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
        <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
      </div>
      <div class="topbar-actions">@yield('topbar-actions')</div>
    </header>
    <main class="page-content">
      @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@stack('scripts')
<script>
document.addEventListener('click', function(e) {
  const sb = document.getElementById('sidebar');
  if (window.innerWidth <= 900 && sb.classList.contains('open') && !sb.contains(e.target)) {
    sb.classList.remove('open');
  }
});
</script>
</body>
</html>
