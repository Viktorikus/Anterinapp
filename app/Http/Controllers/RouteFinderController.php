<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TripHistory;
use Illuminate\Http\Request;

class RouteFinderController extends Controller
{
    // Defined road segments for Bandung (rule-based alternative routing)
    private array $roadSegments = [
        'soekarno_hatta' => [
            'name'  => 'Jl. Soekarno-Hatta',
            'lat'   => -6.9350, 'lng' => 107.6100,
            'alt'   => 'jl_buah_batu',
        ],
        'jl_buah_batu' => [
            'name'  => 'Jl. Buah Batu',
            'lat'   => -6.9390, 'lng' => 107.6310,
            'alt'   => 'soekarno_hatta',
        ],
        'pasteur' => [
            'name'  => 'Jl. Dr. Djundjunan (Pasteur)',
            'lat'   => -6.8843, 'lng' => 107.5783,
            'alt'   => 'jl_sudirman',
        ],
        'jl_sudirman' => [
            'name'  => 'Jl. Sudirman',
            'lat'   => -6.9175, 'lng' => 107.5991,
            'alt'   => 'jl_merdeka',
        ],
        'jl_merdeka' => [
            'name'  => 'Jl. Merdeka',
            'lat'   => -6.9141, 'lng' => 107.6100,
            'alt'   => 'jl_diponegoro',
        ],
        'jl_diponegoro' => [
            'name'  => 'Jl. Diponegoro',
            'lat'   => -6.9012, 'lng' => 107.6138,
            'alt'   => 'jl_merdeka',
        ],
        'dago' => [
            'name'  => 'Jl. Ir. H. Djuanda (Dago)',
            'lat'   => -6.8904, 'lng' => 107.6142,
            'alt'   => 'jl_diponegoro',
        ],
        'alun_alun' => [
            'name'  => 'Alun-Alun Bandung',
            'lat'   => -6.9218, 'lng' => 107.6069,
            'alt'   => 'braga',
        ],
        'braga' => [
            'name'  => 'Jl. Braga',
            'lat'   => -6.9170, 'lng' => 107.6086,
            'alt'   => 'alun_alun',
        ],
    ];

    public function index(Request $request)
    {
        $result = null;
        $bookmarks = auth()->check()
            ? auth()->user()->bookmarks()->orderByDesc('use_count')->get()
            : collect();

        if ($request->filled('origin_lat') && $request->filled('destination_lat')) {
            $result = $this->findRoute($request);

            // Save to history if user is logged in
            if (auth()->check()) {
                TripHistory::create([
                    'user_id'          => auth()->id(),
                    'origin_name'      => $request->origin_name ?? 'Titik Asal',
                    'origin_lat'       => $request->origin_lat,
                    'origin_lng'       => $request->origin_lng,
                    'destination_name' => $request->destination_name ?? 'Tujuan',
                    'destination_lat'  => $request->destination_lat,
                    'destination_lng'  => $request->destination_lng,
                    'route_taken'      => $result,
                ]);
            }
        }

        return view('route-finder.index', compact('result', 'bookmarks', 'request'));
    }

    private function findRoute(Request $request): array
    {
        $originLat = (float) $request->origin_lat;
        $originLng = (float) $request->origin_lng;
        $destLat   = (float) $request->destination_lat;
        $destLng   = (float) $request->destination_lng;

        // Check which segments have active congestion reports
        $congestedSegments = [];
        foreach ($this->roadSegments as $key => $segment) {
            $congestCount = Report::where('status', 'active')
                ->where('type', 'macet')
                ->whereBetween('latitude', [$segment['lat'] - 0.01, $segment['lat'] + 0.01])
                ->whereBetween('longitude', [$segment['lng'] - 0.01, $segment['lng'] + 0.01])
                ->count();

            if ($congestCount >= 2) {
                $congestedSegments[$key] = $segment;
            }
        }

        $hasAlternative = !empty($congestedSegments);
        $avoidedRoads   = [];
        $alternativeRoads = [];

        foreach ($congestedSegments as $key => $seg) {
            $avoidedRoads[]     = $seg['name'];
            $altKey = $seg['alt'];
            if (isset($this->roadSegments[$altKey])) {
                $alternativeRoads[] = $this->roadSegments[$altKey]['name'];
            }
        }

        return [
            'origin'      => ['lat' => $originLat, 'lng' => $originLng, 'name' => $request->origin_name ?? 'Asal'],
            'destination' => ['lat' => $destLat,   'lng' => $destLng,   'name' => $request->destination_name ?? 'Tujuan'],
            'has_alternative' => $hasAlternative,
            'avoided_roads'   => $avoidedRoads,
            'alternative_roads' => $alternativeRoads,
            'congested_segments' => array_values($congestedSegments),
            'primary_color'   => '#FF6B6B',
            'alt_color'       => '#4D96FF',
            'estimated_primary'  => rand(20, 45) . ' menit',
            'estimated_alt'      => $hasAlternative ? rand(25, 55) . ' menit' : null,
        ];
    }
}
