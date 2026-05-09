<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RouteFinderController;
use App\Http\Controllers\TransitRouteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/transit-routes', [TransitRouteController::class, 'index'])->name('transit-routes.index');
Route::get('/transit-routes/{transitRoute}', [TransitRouteController::class, 'show'])->name('transit-routes.show');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

// API - vehicle positions (polling)
Route::get('/api/vehicles/positions', [MonitoringController::class, 'positions'])->name('api.vehicles.positions');
Route::post('/api/vehicles/{vehicle}/position', [MonitoringController::class, 'updatePosition'])->middleware('auth')->name('api.vehicles.update-position');


// Auth required routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reports
    Route::resource('reports', ReportController::class)->except(['update', 'edit']);
    Route::post('/reports/{report}/vote', [ReportController::class, 'vote'])->name('reports.vote');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::post('/bookmarks/{bookmark}/use', [BookmarkController::class, 'use'])->name('bookmarks.use');

    // Trip History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    // Route Finder
    Route::get('/route-finder', [RouteFinderController::class, 'index'])->name('route-finder.index');

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Vehicles
        Route::resource('vehicles', Admin\VehicleController::class);

        // Announcements
        Route::resource('announcements', Admin\AnnouncementController::class);
    });
});

require __DIR__.'/auth.php';
