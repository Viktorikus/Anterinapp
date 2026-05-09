<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclePosition;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('latestPosition')
            ->where('is_active', true)
            ->get();
        return view('monitoring.index', compact('vehicles'));
    }

    public function positions()
    {
        $vehicles = Vehicle::with('latestPosition')
            ->where('is_active', true)
            ->get()
            ->map(function ($v) {
                $pos = $v->latestPosition;
                return [
                    'id'                => $v->id,
                    'name'              => $v->name,
                    'trayek_code'       => $v->trayek_code,
                    'trayek_name'       => $v->trayek_name,
                    'type'              => $v->type,
                    'status'            => $v->status,
                    'status_label'      => $v->status_label,
                    'status_color'      => $v->status_color,
                    'driver_name'       => $v->driver_name,
                    'plate_number'      => $v->plate_number,
                    'latitude'          => $pos?->latitude,
                    'longitude'         => $pos?->longitude,
                    'speed'             => $pos?->speed,
                    'estimated_arrival' => $pos?->estimated_arrival,
                    'updated_at'        => $pos?->updated_at?->diffForHumans(),
                ];
            });

        return response()->json($vehicles);
    }

    public function updatePosition(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'latitude'          => 'required|numeric|between:-90,90',
            'longitude'         => 'required|numeric|between:-180,180',
            'status'            => 'required|in:berangkat,berhenti,menuju_halte',
            'estimated_arrival' => 'nullable|string|max:50',
        ]);

        $vehicle->update(['status' => $request->status]);

        VehiclePosition::create([
            'vehicle_id'        => $vehicle->id,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'speed'             => $request->speed ?? 0,
            'heading'           => $request->heading ?? 0,
            'estimated_arrival' => $request->estimated_arrival,
            'updated_by'        => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Posisi diperbarui']);
    }
}
