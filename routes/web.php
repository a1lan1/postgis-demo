<?php

use App\Http\Controllers\Api\DistanceController;
use App\Http\Controllers\ClinicController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::controller(ClinicController::class)
    ->name('clinics.')
    ->group(function (): void {
        Route::get('', 'index')->name('index');
        Route::post('', 'store')->name('store');
        Route::get('autocomplete', 'autocomplete')->name('autocomplete');
    });

Route::get('/distance', [DistanceController::class, 'calculate']);

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
