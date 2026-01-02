<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WisudawanController;
use App\Http\Controllers\KursiController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\FotoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Wisudawan management
    Route::get('/wisudawan', [WisudawanController::class, 'index'])->name('wisudawan.index');
    Route::post('/wisudawan', [WisudawanController::class, 'store'])->name('wisudawan.store');
    Route::get('/wisudawan/{id}', [WisudawanController::class, 'show'])->name('wisudawan.show');
    Route::put('/wisudawan/{id}', [WisudawanController::class, 'update'])->name('wisudawan.update');
    Route::delete('/wisudawan/{id}', [WisudawanController::class, 'destroy'])->name('wisudawan.destroy');
    
    // Kursi management
    Route::get('/kursi', [KursiController::class, 'index'])->name('kursi.index');
    Route::post('/kursi/assign', [KursiController::class, 'assignSeat'])->name('kursi.assign');
    Route::post('/kursi/{id}/unassign', [KursiController::class, 'unassignSeat'])->name('kursi.unassign');
    Route::get('/kursi/search', [KursiController::class, 'search'])->name('kursi.search');
    Route::post('/kursi/auto-arrange', [KursiController::class, 'autoArrange'])->name('kursi.auto-arrange');
    Route::post('/kursi', [KursiController::class, 'store'])->name('kursi.store');
    Route::put('/kursi/{id}', [KursiController::class, 'update'])->name('kursi.update');
    Route::delete('/kursi/{id}', [KursiController::class, 'destroy'])->name('kursi.destroy');
    
    // Presensi (Attendance)
    Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi/scan', [PresensiController::class, 'scan'])->name('presensi.scan');
    Route::get('/presensi/export', [PresensiController::class, 'export'])->name('presensi.export');
    Route::delete('/presensi/clear', [PresensiController::class, 'clear'])->name('presensi.clear');
    
    // Foto Wisuda
    Route::get('/foto', [FotoController::class, 'index'])->name('foto.index');
    Route::post('/foto', [FotoController::class, 'store'])->name('foto.store');
    Route::put('/foto/{id}', [FotoController::class, 'update'])->name('foto.update');
    Route::delete('/foto/{id}', [FotoController::class, 'destroy'])->name('foto.destroy');
    
    // Reports
    Route::get('/laporan', function () {
        return view('admin.laporan.index');
    })->name('laporan.index');
    
    // QR Code management
    Route::get('/qrcode', function () {
        return view('admin.qrcode.index');
    })->name('qrcode.index');
});

// Wisudawan routes
Route::prefix('wisudawan')->middleware(['auth', 'role:wisudawan'])->name('wisudawan.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'wisudawanDashboard'])->name('dashboard');
    Route::get('/profile', [WisudawanController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [WisudawanController::class, 'updateProfile'])->name('profile.update');
    Route::get('/kursi', [KursiController::class, 'mySeat'])->name('kursi');
    Route::get('/qrcode', function () {
        return view('wisudawan.qrcode');
    })->name('qrcode');
    Route::get('/foto', [FotoController::class, 'getAll'])->name('foto');
});
