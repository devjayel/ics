<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $personnelsCount = \App\Models\Personnel::count();
        $rulsCount = \App\Models\Rul::count();
        $certificatesCount = \App\Models\Certificate::count();
        
        // Recent records
        $recentPersonnels = \App\Models\Personnel::latest()->take(5)->get();
        $recentRuls = \App\Models\Rul::latest()->take(5)->get();
        $recentCertificates = \App\Models\Certificate::with('rul')->latest()->take(5)->get();
        
        return Inertia::render('dashboard', [
            'stats' => [
                'personnels' => $personnelsCount,
                'ruls' => $rulsCount,
                'certificates' => $certificatesCount,
            ],
            'recent' => [
                'personnels' => $recentPersonnels,
                'ruls' => $recentRuls,
                'certificates' => $recentCertificates,
            ],
        ]);
    })->name('dashboard');

    Route::resource('ruls', 'App\Http\Controllers\ResourceUnitLeadersController');
    Route::resource('personnels', 'App\Http\Controllers\PersonnelController');
    Route::resource('certificates', 'App\Http\Controllers\CertificatesController');
    
    // User Management Routes
    Route::resource('management', 'App\Http\Controllers\UserController')->names([
        'index' => 'management.index',
        'create' => 'management.create',
        'store' => 'management.store',
        'edit' => 'management.edit',
        'update' => 'management.update',
        'destroy' => 'management.destroy',
    ]);
});

require __DIR__.'/settings.php';
