<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePosition;
use App\Models\TransitRoute;
use App\Models\RouteStop;
use App\Models\Schedule;
use App\Models\Report;
use App\Models\ReportVote;
use App\Models\Bookmark;
use App\Models\TripHistory;
use App\Models\Announcement;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── USERS ───────────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin ANTERIN',
            'email'    => 'admin@anterin.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '081200000001',
        ]);

        $users = collect([
            ['name' => 'Budi Santoso',   'email' => 'budi@mail.com'],
            ['name' => 'Siti Rahayu',    'email' => 'siti@mail.com'],
            ['name' => 'Andi Wijaya',    'email' => 'andi@mail.com'],
            ['name' => 'Dewi Permata',   'email' => 'dewi@mail.com'],
            ['name' => 'Rizky Pratama',  'email' => 'rizky@mail.com'],
        ])->map(fn($u) => User::create([
            ...$u,
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]));

        // ─── TRANSIT ROUTES (Bandung) ────────────────────────────────────────
        $routes = [
            [
                'name'        => 'Cicaheum - Cibeureum',
                'code'        => 'A1',
                'color'       => '#FF6B6B',
                'description' => 'Trayek utama menghubungkan Cicaheum dan Cibeureum melewati pusat kota Bandung',
                'start_point' => 'Terminal Cicaheum',
                'end_point'   => 'Terminal Cibeureum',
                'distance_km' => 18.5,
                'stops'       => [
                    ['name' => 'Terminal Cicaheum',    'lat' => -6.9018, 'lng' => 107.6603],
                    ['name' => 'Jl. Ahmad Yani',       'lat' => -6.9047, 'lng' => 107.6502],
                    ['name' => 'Alun-Alun Bandung',    'lat' => -6.9218, 'lng' => 107.6069],
                    ['name' => 'Jl. Sudirman',         'lat' => -6.9175, 'lng' => 107.5991],
                    ['name' => 'Terminal Cibeureum',   'lat' => -6.9122, 'lng' => 107.5612],
                ],
            ],
            [
                'name'        => 'Dago - Leuwipanjang',
                'code'        => 'B2',
                'color'       => '#4D96FF',
                'description' => 'Menghubungkan kawasan Dago di utara dengan Terminal Leuwipanjang di selatan',
                'start_point' => 'Dago',
                'end_point'   => 'Terminal Leuwipanjang',
                'distance_km' => 14.2,
                'stops'       => [
                    ['name' => 'Dago',                  'lat' => -6.8811, 'lng' => 107.6139],
                    ['name' => 'Simpang Dago',          'lat' => -6.8904, 'lng' => 107.6142],
                    ['name' => 'Jl. Diponegoro',        'lat' => -6.9012, 'lng' => 107.6138],
                    ['name' => 'Jl. Merdeka',           'lat' => -6.9141, 'lng' => 107.6100],
                    ['name' => 'Jl. Soekarno-Hatta',   'lat' => -6.9443, 'lng' => 107.6152],
                    ['name' => 'Terminal Leuwipanjang', 'lat' => -6.9597, 'lng' => 107.6062],
                ],
            ],
            [
                'name'        => 'Antapani - Ciroyom',
                'code'        => 'C3',
                'color'       => '#6BCB77',
                'description' => 'Trayek melintasi Jl. Supratman dan pusat kota menuju Ciroyom',
                'start_point' => 'Antapani',
                'end_point'   => 'Ciroyom',
                'distance_km' => 11.8,
                'stops'       => [
                    ['name' => 'Antapani',         'lat' => -6.9119, 'lng' => 107.6558],
                    ['name' => 'Jl. Supratman',    'lat' => -6.9095, 'lng' => 107.6381],
                    ['name' => 'Braga',             'lat' => -6.9170, 'lng' => 107.6086],
                    ['name' => 'Pasar Baru',        'lat' => -6.9183, 'lng' => 107.6031],
                    ['name' => 'Ciroyom',           'lat' => -6.9163, 'lng' => 107.5882],
                ],
            ],
            [
                'name'        => 'Elang - Kebon Kalapa',
                'code'        => 'D4',
                'color'       => '#FFD93D',
                'description' => 'Menghubungkan kawasan barat Bandung dengan pusat kota',
                'start_point' => 'Terminal Elang',
                'end_point'   => 'Kebon Kalapa',
                'distance_km' => 9.3,
                'stops'       => [
                    ['name' => 'Terminal Elang',   'lat' => -6.9217, 'lng' => 107.5782],
                    ['name' => 'Jl. Sudirman',     'lat' => -6.9175, 'lng' => 107.5991],
                    ['name' => 'Jl. Otto Iskandardinata', 'lat' => -6.9198, 'lng' => 107.6051],
                    ['name' => 'Kebon Kalapa',     'lat' => -6.9212, 'lng' => 107.6095],
                ],
            ],
        ];

        foreach ($routes as $rd) {
            $stopData = $rd['stops'];
            unset($rd['stops']);
            $route = TransitRoute::create($rd);

            foreach ($stopData as $i => $stop) {
                RouteStop::create([
                    'transit_route_id' => $route->id,
                    'name'             => $stop['name'],
                    'latitude'         => $stop['lat'],
                    'longitude'        => $stop['lng'],
                    'order_number'     => $i + 1,
                ]);
            }

            // Schedules
            $departures = ['05:00', '06:00', '07:00', '08:00', '09:00', '11:00', '13:00', '15:00', '17:00', '18:30', '20:00'];
            foreach ($departures as $dep) {
                $depTime = Carbon::createFromFormat('H:i', $dep);
                $arrTime = $depTime->copy()->addMinutes(rand(45, 90));
                Schedule::create([
                    'transit_route_id' => $route->id,
                    'departure_time'   => $dep . ':00',
                    'arrival_time'     => $arrTime->format('H:i:s'),
                    'days_of_week'     => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                    'notes'            => null,
                ]);
            }
        }

        // ─── VEHICLES (Bandung) ──────────────────────────────────────────────
        $vehicles = [
            ['name' => 'Angkot A1-001', 'trayek_code' => 'A1', 'trayek_name' => 'Cicaheum - Cibeureum', 'type' => 'angkot', 'status' => 'berangkat', 'plate_number' => 'D 1234 AB', 'driver_name' => 'Pak Ujang', 'lat' => -6.9047, 'lng' => 107.6502],
            ['name' => 'Angkot A1-002', 'trayek_code' => 'A1', 'trayek_name' => 'Cicaheum - Cibeureum', 'type' => 'angkot', 'status' => 'menuju_halte', 'plate_number' => 'D 1235 AB', 'driver_name' => 'Pak Asep', 'lat' => -6.9175, 'lng' => 107.5991],
            ['name' => 'Bus B2-001', 'trayek_code' => 'B2', 'trayek_name' => 'Dago - Leuwipanjang', 'type' => 'bus', 'status' => 'berangkat', 'plate_number' => 'D 5001 BD', 'driver_name' => 'Pak Dedi', 'lat' => -6.8904, 'lng' => 107.6142],
            ['name' => 'Bus B2-002', 'trayek_code' => 'B2', 'trayek_name' => 'Dago - Leuwipanjang', 'type' => 'bus', 'status' => 'berhenti', 'plate_number' => 'D 5002 BD', 'driver_name' => 'Bu Yuni', 'lat' => -6.9443, 'lng' => 107.6152],
            ['name' => 'Angkot C3-001', 'trayek_code' => 'C3', 'trayek_name' => 'Antapani - Ciroyom', 'type' => 'angkot', 'status' => 'berangkat', 'plate_number' => 'D 2301 CE', 'driver_name' => 'Pak Iwan', 'lat' => -6.9095, 'lng' => 107.6381],
            ['name' => 'Angkot D4-001', 'trayek_code' => 'D4', 'trayek_name' => 'Elang - Kebon Kalapa', 'type' => 'angkot', 'status' => 'menuju_halte', 'plate_number' => 'D 3401 DF', 'driver_name' => 'Pak Hendra', 'lat' => -6.9175, 'lng' => 107.5991],
        ];

        foreach ($vehicles as $vd) {
            $lat = $vd['lat'];
            $lng = $vd['lng'];
            unset($vd['lat'], $vd['lng']);
            $v = Vehicle::create(array_merge($vd, ['capacity' => rand(12, 40)]));

            VehiclePosition::create([
                'vehicle_id'        => $v->id,
                'latitude'          => $lat,
                'longitude'         => $lng,
                'speed'             => rand(0, 50),
                'heading'           => rand(0, 359),
                'estimated_arrival' => rand(5, 20) . ' menit',
                'updated_by'        => $admin->id,
            ]);
        }

        // ─── REPORTS (Bandung) ───────────────────────────────────────────────
        $reportData = [
            ['type' => 'macet', 'title' => 'Kemacetan Parah Jl. Soekarno-Hatta', 'description' => 'Kemacetan sangat parah akibat penyempitan jalan karena proyek drainase. Antrian kendaraan mencapai 2 km.', 'lat' => -6.9350, 'lng' => 107.6100, 'location_name' => 'Jl. Soekarno-Hatta', 'hours_ago' => 1],
            ['type' => 'kecelakaan', 'title' => 'Kecelakaan di Simpang Pasteur', 'description' => 'Tabrakan antara motor dan mobil di Simpang Pasteur. Satu lajur tertutup, polisi sudah di lokasi.', 'lat' => -6.8972, 'lng' => 107.5832, 'location_name' => 'Simpang Pasteur', 'hours_ago' => 2],
            ['type' => 'macet', 'title' => 'Macet Alun-Alun Bandung', 'description' => 'Kemacetan akibat banyak pejalan kaki menyeberang dan pedagang kaki lima memblokir jalan.', 'lat' => -6.9218, 'lng' => 107.6069, 'location_name' => 'Alun-Alun Bandung', 'hours_ago' => 3],
            ['type' => 'jalan_rusak', 'title' => 'Jalan Berlubang Jl. Buah Batu', 'description' => 'Terdapat 3 lubang besar di jalur tengah yang berbahaya bagi pengendara motor. Perlu segera diperbaiki.', 'lat' => -6.9390, 'lng' => 107.6310, 'location_name' => 'Jl. Buah Batu', 'hours_ago' => 5],
            ['type' => 'macet', 'title' => 'Antrian Panjang Pintu Tol Pasteur', 'description' => 'Antrian panjang di pintu masuk Tol Pasteur, menyebabkan kemacetan hingga Jl. Dr. Djundjunan.', 'lat' => -6.8843, 'lng' => 107.5783, 'location_name' => 'Tol Pasteur', 'hours_ago' => 1],
            ['type' => 'lainnya', 'title' => 'Lampu Merah Mati Jl. Merdeka', 'description' => 'Lampu merah di persimpangan Jl. Merdeka - Jl. Diponegoro mati sejak tadi pagi.', 'lat' => -6.9141, 'lng' => 107.6100, 'location_name' => 'Jl. Merdeka', 'hours_ago' => 4],
            ['type' => 'macet', 'title' => 'Kemacetan Dago Atas', 'description' => 'Kemacetan di kawasan Dago Atas akibat wisatawan akhir pekan.', 'lat' => -6.8720, 'lng' => 107.6160, 'location_name' => 'Dago Atas', 'hours_ago' => 2],
            ['type' => 'kecelakaan', 'title' => 'Motor Jatuh di Jl. Riau', 'description' => 'Pengendara motor jatuh karena menghindari lubang, korban sudah dilarikan ke klinik.', 'lat' => -6.9010, 'lng' => 107.6230, 'location_name' => 'Jl. Riau', 'hours_ago' => 3],
        ];

        $allUsers = $users->prepend($admin);
        $reports = [];

        foreach ($reportData as $i => $rd) {
            $report = Report::create([
                'user_id'       => $allUsers[$i % $allUsers->count()]->id,
                'type'          => $rd['type'],
                'title'         => $rd['title'],
                'description'   => $rd['description'],
                'latitude'      => $rd['lat'],
                'longitude'     => $rd['lng'],
                'location_name' => $rd['location_name'],
                'status'        => 'active',
                'expires_at'    => now()->addHours(6 - $rd['hours_ago']),
                'created_at'    => now()->subHours($rd['hours_ago']),
                'updated_at'    => now()->subHours($rd['hours_ago']),
            ]);
            $reports[] = $report;
        }

        // Votes for reports
        $voteTypes = ['lancar', 'padat', 'macet'];
        foreach ($reports as $report) {
            foreach ($users as $u) {
                if (rand(0, 1)) {
                    ReportVote::create([
                        'report_id' => $report->id,
                        'user_id'   => $u->id,
                        'vote_type' => $voteTypes[rand(0, 2)],
                    ]);
                }
            }
        }

        // ─── BOOKMARKS ───────────────────────────────────────────────────────
        $bookmarkData = [
            ['name' => 'Rumah → Kantor', 'origin_name' => 'Antapani', 'origin_lat' => -6.9119, 'origin_lng' => 107.6558, 'destination_name' => 'Jl. Asia Afrika', 'destination_lat' => -6.9218, 'destination_lng' => 107.6069],
            ['name' => 'Rumah → Kampus', 'origin_name' => 'Ciwastra', 'origin_lat' => -6.9468, 'origin_lng' => 107.6590, 'destination_name' => 'Universitas Padjadjaran', 'destination_lat' => -6.9275, 'destination_lng' => 107.7706],
            ['name' => 'Kos → Kampus ITB', 'origin_name' => 'Dago', 'origin_lat' => -6.8811, 'origin_lng' => 107.6139, 'destination_name' => 'ITB Bandung', 'destination_lat' => -6.8915, 'destination_lng' => 107.6107],
        ];

        foreach ($users->take(3) as $i => $u) {
            $bm = Bookmark::create(array_merge($bookmarkData[$i], [
                'user_id'   => $u->id,
                'use_count' => rand(5, 30),
            ]));

            $historyData = $bookmarkData[$i];
            unset($historyData['name']);
            TripHistory::create(array_merge($historyData, [
                'user_id'     => $u->id,
                'bookmark_id' => $bm->id,
            ]));
        }

        // ─── ANNOUNCEMENTS ───────────────────────────────────────────────────
        $announcementData = [
            ['title' => 'Penutupan Sementara Jl. Asia Afrika', 'content' => 'Dalam rangka Bandung Light Festival, Jl. Asia Afrika akan ditutup untuk kendaraan bermotor pada tanggal 10-12 Mei 2025 pukul 17.00-23.00 WIB. Mohon gunakan rute alternatif.', 'type' => 'closure', 'is_active' => true],
            ['title' => 'Perbaikan Jalan Jl. Soekarno-Hatta KM 6-8', 'content' => 'Akan dilakukan perbaikan perkerasan jalan di Jl. Soekarno-Hatta KM 6-8 mulai 6 Mei 2025. Harap mewaspadai penyempitan jalan dan ikuti arahan petugas.', 'type' => 'repair', 'is_active' => true],
            ['title' => 'Perubahan Rute Trayek A1 (Sementara)', 'content' => 'Trayek A1 Cicaheum-Cibeureum sementara akan melewati Jl. Ahmad Yani - Jl. Gatot Subroto karena perbaikan Jl. Asia Afrika. Estimasi normal kembali 15 Mei 2025.', 'type' => 'route_change', 'is_active' => true],
            ['title' => 'Bandung Culinary Festival 2025', 'content' => 'Festival kuliner tahunan Kota Bandung akan diadakan di Gasibu pada 17-19 Mei 2025. Antisipasi kepadatan arus lalu lintas di sekitar area Gasibu.', 'type' => 'event', 'is_active' => true],
            ['title' => 'Armada Bus Baru Trans Metro Bandung', 'content' => 'Pemerintah Kota Bandung telah menambah 20 unit armada Bus Trans Metro Bandung untuk koridor Cicaheum-Cibeureum. Layanan mulai beroperasi 1 Juni 2025.', 'type' => 'info', 'is_active' => true],
        ];

        foreach ($announcementData as $ad) {
            Announcement::create(array_merge($ad, [
                'admin_id'   => $admin->id,
                'starts_at'  => now()->subDays(rand(1, 3)),
                'expires_at' => now()->addDays(rand(7, 30)),
            ]));
        }
    }
}
