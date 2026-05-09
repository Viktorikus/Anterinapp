@extends('layouts.app')
@section('title', $report->title)
@section('page-title', 'Detail Laporan')
@section('topbar-actions')
  <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
@endsection

@section('content')
<div class="grid-2" style="gap:24px;align-items:start">
  <div>
    <div class="card mb-4">
      <div class="card-body">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
          <span style="font-size:36px">{{ $report->type_icon }}</span>
          <div>
            <span class="badge {{ $report->type=='macet'?'badge-red':($report->type=='kecelakaan'?'badge-yellow':'badge-blue') }} mb-2">{{ $report->type_label }}</span>
            <h2 style="font-size:20px;font-weight:800;color:#E6F1FF">{{ $report->title }}</h2>
          </div>
        </div>

        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;font-size:12px;color:#8892B0">
          <span>👤 {{ $report->user->name }}</span>
          <span>📍 {{ $report->location_name ?? 'Bandung' }}</span>
          <span>🕐 {{ $report->created_at->diffForHumans() }}</span>
          <span>⏳ Exp: {{ $report->expires_at?->format('H:i, d M') }}</span>
        </div>

        <span class="badge {{ $report->status=='active'?'badge-teal':($report->status=='expired'?'badge-gray':'badge-green') }}">
          {{ $report->status=='active'?'🟢 Aktif':($report->status=='expired'?'⏰ Kedaluwarsa':'✅ Resolved') }}
        </span>

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
          <p style="color:#CCD6F6;font-size:14px;line-height:1.8">{{ $report->description }}</p>
        </div>

        @if($report->photo_path)
        <div style="margin-top:16px">
          <img src="{{ $report->photo_url }}" alt="Foto Laporan" style="width:100%;border-radius:10px;max-height:300px;object-fit:cover">
        </div>
        @endif
      </div>
    </div>

    {{-- Voting --}}
    @if($report->status === 'active')
    <div class="card mb-4">
      <div class="card-header"><span class="card-title">🗳️ Voting Kondisi</span></div>
      <div class="card-body">
        <p style="font-size:13px;color:#8892B0;margin-bottom:16px">Seberapa parah kondisi di lokasi ini saat ini?</p>
        <form method="POST" action="{{ route('reports.vote', $report) }}">
          @csrf
          <div style="display:flex;gap:10px">
            <button type="submit" name="vote_type" value="macet" class="vote-btn {{ $userVote?->vote_type=='macet'?'active-macet':'' }}">
              🚨 Macet <span style="font-size:11px">({{ $macetCount }})</span>
            </button>
            <button type="submit" name="vote_type" value="padat" class="vote-btn {{ $userVote?->vote_type=='padat'?'active-padat':'' }}">
              ⚠️ Padat <span style="font-size:11px">({{ $padatCount }})</span>
            </button>
            <button type="submit" name="vote_type" value="lancar" class="vote-btn {{ $userVote?->vote_type=='lancar'?'active-lancar':'' }}">
              ✅ Lancar <span style="font-size:11px">({{ $lancarCount }})</span>
            </button>
          </div>
        </form>

        {{-- Vote bars --}}
        @php $totalVotes = $macetCount + $padatCount + $lancarCount; @endphp
        @if($totalVotes > 0)
        <div style="margin-top:16px">
          <div style="font-size:12px;color:#8892B0;margin-bottom:8px">Total {{ $totalVotes }} suara</div>
          @foreach(['macet'=>['🚨','#FF6B6B',$macetCount], 'padat'=>['⚠️','#FFD93D',$padatCount], 'lancar'=>['✅','#6BCB77',$lancarCount]] as $type=>[$icon,$color,$count])
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
            <span style="width:50px;font-size:12px">{{ $icon }} {{ ucfirst($type) }}</span>
            <div style="flex:1;background:rgba(255,255,255,0.05);border-radius:4px;height:8px">
              <div style="width:{{ $totalVotes>0?round($count/$totalVotes*100):0 }}%;background:{{ $color }};height:100%;border-radius:4px;transition:width .5s"></div>
            </div>
            <span style="font-size:12px;color:#8892B0;width:28px;text-align:right">{{ $count }}</span>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
    @endif

    @if(auth()->id()===$report->user_id || auth()->user()->isAdmin())
    <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Hapus laporan ini?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus Laporan</button>
    </form>
    @endif
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">📍 Lokasi Kejadian</span></div>
    <div class="map-container" style="border-radius:0 0 12px 12px"><div id="report-map" style="height:420px"></div></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const map = L.map('report-map').setView([{{ $report->latitude }}, {{ $report->longitude }}], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap'}).addTo(map);
const icon = L.divIcon({
  html: `<div style="background:#FF6B6B;border:3px solid #fff;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:15px;box-shadow:0 2px 8px rgba(0,0,0,.5)">{{ $report->type_icon }}</div>`,
  iconSize:[32,32],iconAnchor:[16,16],className:''
});
L.marker([{{ $report->latitude }}, {{ $report->longitude }}], {icon}).addTo(map)
  .bindPopup('<b>{{ addslashes($report->title) }}</b><br><small>{{ addslashes($report->location_name ?? 'Bandung') }}</small>')
  .openPopup();
</script>
@endpush
