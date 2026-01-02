<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wisudawan;
use App\Models\Kursi;
use App\Models\Presensi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard with statistics
     */
    public function index()
    {
        $stats = [
            'total_wisudawan' => Wisudawan::count(),
            'total_hadir' => Presensi::count(),
            'belum_hadir' => Wisudawan::count() - Presensi::count(),
            'kursi_terisi' => Kursi::occupied()->count(),
            'kursi_kosong' => Kursi::empty()->count(),
            'total_kursi' => Kursi::count(),
        ];

        // Stats per hari
        $stats['kursi_hari1'] = Kursi::byHari(1)->occupied()->count();
        $stats['kursi_hari2'] = Kursi::byHari(2)->occupied()->count();

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show wisudawan dashboard
     */
    public function wisudawanDashboard(Request $request)
    {
        $user = $request->user();
        $wisudawan = $user->wisudawan;
        
        $data = [
            'user' => $user,
            'wisudawan' => $wisudawan,
            'kursi' => $wisudawan ? $wisudawan->kursi : null,
            'presensi' => $wisudawan ? $wisudawan->presensi : null,
        ];

        return view('wisudawan.dashboard', $data);
    }

    /**
     * API: Get dashboard stats
     */
    public function getStats()
    {
        $stats = [
            'total_wisudawan' => Wisudawan::count(),
            'total_hadir' => Presensi::count(),
            'belum_hadir' => Wisudawan::count() - Presensi::count(),
            'kursi_terisi' => Kursi::occupied()->count(),
            'kursi_kosong' => Kursi::empty()->count(),
            'persentase_hadir' => Wisudawan::count() > 0 
                ? round((Presensi::count() / Wisudawan::count()) * 100) 
                : 0,
        ];

        return response()->json($stats);
    }
}
