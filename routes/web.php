<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecycleBinController;
use Illuminate\Support\Facades\Route;

// Guest authentication routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Party Management
    Route::get('/parties', [PartyController::class, 'index'])->name('parties.index');
    Route::get('/parties/map', [PartyController::class, 'map'])->name('parties.map');
    Route::get('/parties/create', [PartyController::class, 'create'])->name('parties.create');
    Route::post('/parties', [PartyController::class, 'store'])->name('parties.store');
    Route::post('/parties/extract-coordinates', [PartyController::class, 'extractCoordinates'])->name('parties.extract_coordinates');
    Route::get('/parties/{party}/edit', [PartyController::class, 'edit'])->name('parties.edit');
    Route::put('/parties/{party}', [PartyController::class, 'update'])->name('parties.update');
    Route::delete('/parties/{party}', [PartyController::class, 'destroy'])->name('parties.destroy');

    // Excel & CSV Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    Route::get('/import/template', [ImportController::class, 'template'])->name('import.template');
    Route::get('/import/errors', [ImportController::class, 'downloadErrors'])->name('import.errors');

    // Recycle Bin
    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('recycle_bin.index');
    Route::post('/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore'])->name('recycle_bin.restore');
    Route::delete('/recycle-bin/{id}', [RecycleBinController::class, 'forceDelete'])->name('recycle_bin.forceDelete');

    // Admin Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
