<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehiclePosition;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('latestPosition')->latest()->paginate(15);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'trayek_code'  => 'required|string|max:20',
            'trayek_name'  => 'required|string|max:100',
            'type'         => 'required|in:bus,angkot,kereta,transjakarta',
            'capacity'     => 'required|integer|min:1|max:200',
            'plate_number' => 'nullable|string|max:20',
            'driver_name'  => 'nullable|string|max:100',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
        ]);

        $vehicle = Vehicle::create($request->only([
            'name','trayek_code','trayek_name','type','capacity','plate_number','driver_name'
        ]));

        if ($request->latitude && $request->longitude) {
            VehiclePosition::create([
                'vehicle_id'  => $vehicle->id,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'updated_by'  => auth()->id(),
            ]);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function edit(Vehicle $vehicle)
    {
        $vehicle->load('latestPosition');
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'trayek_code'  => 'required|string|max:20',
            'trayek_name'  => 'required|string|max:100',
            'type'         => 'required|in:bus,angkot,kereta,transjakarta',
            'status'       => 'required|in:berangkat,berhenti,menuju_halte',
            'capacity'     => 'required|integer|min:1|max:200',
            'plate_number' => 'nullable|string|max:20',
            'driver_name'  => 'nullable|string|max:100',
            'is_active'    => 'boolean',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'estimated_arrival' => 'nullable|string|max:50',
        ]);

        $vehicle->update($request->only([
            'name','trayek_code','trayek_name','type','status','capacity','plate_number','driver_name','is_active'
        ]));

        if ($request->latitude && $request->longitude) {
            VehiclePosition::create([
                'vehicle_id'        => $vehicle->id,
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'estimated_arrival' => $request->estimated_arrival,
                'updated_by'        => auth()->id(),
            ]);
        }

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'Kendaraan dihapus.');
    }
}
