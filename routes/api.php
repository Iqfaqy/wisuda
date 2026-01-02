<?php

use Illuminate\Http\Request;
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
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $data = $user->toArray();
        if ($user->wisudawan) {
            $data = array_merge($data, $user->wisudawan->toArray());
            $data['kursi'] = $user->wisudawan->kursi ? $user->wisudawan->kursi->kode_kursi : null;
            $data['presensi'] = $user->wisudawan->presensi ? true : false;
        }
        return response()->json($data);
    });
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    // Dashboard stats
    Route::get('/stats/dashboard', [DashboardController::class, 'getStats']);
    Route::get('/stats/users', [UserController::class, 'getStats']);
    Route::get('/stats/presensi', [PresensiController::class, 'getStats']);

    // Users CRUD (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);

        // Wisudawan CRUD
        Route::get('/wisudawan', [WisudawanController::class, 'index']);
        Route::post('/wisudawan', [WisudawanController::class, 'store']);
        Route::get('/wisudawan/{id}', [WisudawanController::class, 'show']);
        Route::put('/wisudawan/{id}', [WisudawanController::class, 'update']);
        Route::delete('/wisudawan/{id}', [WisudawanController::class, 'destroy']);
        Route::get('/wisudawan/prodi/list', [WisudawanController::class, 'getProdiList']);

        // Kursi management
        Route::get('/kursi', [KursiController::class, 'index']);
        Route::get('/kursi/section', [KursiController::class, 'getBySection']);
        Route::post('/kursi/assign', [KursiController::class, 'assignSeat']);
        Route::post('/kursi/{id}/unassign', [KursiController::class, 'unassignSeat']);
        Route::get('/kursi/search', [KursiController::class, 'search']);
        Route::post('/kursi/auto-arrange', [KursiController::class, 'autoArrange']);

        // Presensi
        Route::get('/presensi', [PresensiController::class, 'index']);
        Route::post('/presensi/scan', [PresensiController::class, 'scan']);
        Route::get('/presensi/export', [PresensiController::class, 'export']);
        Route::delete('/presensi/clear', [PresensiController::class, 'clear']);

        // Foto
        Route::get('/foto', [FotoController::class, 'index']);
        Route::post('/foto', [FotoController::class, 'store']);
        Route::put('/foto/{id}', [FotoController::class, 'update']);
        Route::delete('/foto/{id}', [FotoController::class, 'destroy']);
    });

    // Wisudawan routes (for logged in wisudawan)
    Route::middleware('role:wisudawan')->group(function () {
        Route::get('/my/profile', [WisudawanController::class, 'profile']);
        Route::get('/my/kursi', [KursiController::class, 'mySeat']);
        Route::get('/my/presensi', [PresensiController::class, 'myPresensi']);
        Route::get('/my/foto', [FotoController::class, 'getAll']);
    });
});
