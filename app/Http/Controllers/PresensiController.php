<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Wisudawan;
use App\Models\Kursi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Display attendance list
     */
    public function index(Request $request)
    {
        $presensi = Presensi::with('wisudawan.user', 'wisudawan.kursi')
            ->orderBy('waktu_scan', 'desc')
            ->paginate(50);

        $stats = $this->getStatsData();

        if ($request->wantsJson()) {
            return response()->json([
                'presensi' => $presensi,
                'stats' => $stats,
            ]);
        }

        return view('admin.presensi.index', compact('presensi', 'stats'));
    }

    /**
     * Process QR code scan for attendance
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $qrCode = trim($request->qr_code);

        // QR code format: could be NIM, seat code, or custom format
        // Try to find wisudawan by NIM or seat code
        $wisudawan = null;

        // Try by NIM
        $wisudawan = Wisudawan::whereHas('user', function ($q) use ($qrCode) {
            $q->where('nim', $qrCode);
        })->first();

        // Try by seat code if not found
        if (!$wisudawan) {
            $kursi = Kursi::where('kode_kursi', $qrCode)->first();
            if ($kursi) {
                $wisudawan = $kursi->wisudawan;
            }
        }

        if (!$wisudawan) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan atau tidak valid',
            ], 404);
        }

        // Check if already attended
        $existingPresensi = Presensi::where('wisudawan_id', $wisudawan->id)->first();
        if ($existingPresensi) {
            return response()->json([
                'success' => false,
                'message' => 'Wisudawan sudah melakukan presensi',
                'wisudawan' => [
                    'name' => $wisudawan->user->name,
                    'nim' => $wisudawan->user->nim,
                    'waktu_scan' => $existingPresensi->waktu_scan,
                ],
            ], 422);
        }

        // Create attendance record
        $presensi = Presensi::create([
            'wisudawan_id' => $wisudawan->id,
            'qr_code' => $qrCode,
            'waktu_scan' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dicatat',
            'wisudawan' => [
                'name' => $wisudawan->user->name,
                'nim' => $wisudawan->user->nim,
                'prodi' => $wisudawan->prodi,
                'kursi' => $wisudawan->kursi ? $wisudawan->kursi->kode_kursi : null,
            ],
            'presensi' => $presensi,
        ]);
    }

    /**
     * Get attendance statistics
     */
    public function getStats()
    {
        return response()->json($this->getStatsData());
    }

    /**
     * Get stats data helper
     */
    private function getStatsData(): array
    {
        $totalWisudawan = Wisudawan::count();
        $hadir = Presensi::count();
        $belumHadir = $totalWisudawan - $hadir;
        $persentase = $totalWisudawan > 0 ? round(($hadir / $totalWisudawan) * 100) : 0;

        return [
            'total_wisudawan' => $totalWisudawan,
            'hadir' => $hadir,
            'belum_hadir' => $belumHadir,
            'persentase' => $persentase,
        ];
    }

    /**
     * Export attendance to CSV
     */
    public function export()
    {
        $presensi = Presensi::with('wisudawan.user', 'wisudawan.kursi')
            ->orderBy('waktu_scan')
            ->get();

        $csv = "No,Nama,NIM,Program Studi,Nomor Kursi,Waktu Scan\n";
        
        foreach ($presensi as $index => $p) {
            $kursi = $p->wisudawan->kursi ? $p->wisudawan->kursi->kode_kursi : '-';
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s\n",
                $index + 1,
                $p->wisudawan->user->name,
                $p->wisudawan->user->nim,
                $p->wisudawan->prodi ?? '-',
                $kursi,
                $p->waktu_scan->format('Y-m-d H:i:s')
            );
        }

        $filename = 'presensi_' . date('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Clear all attendance records
     */
    public function clear()
    {
        Presensi::truncate();

        return response()->json([
            'message' => 'Data presensi berhasil dikosongkan',
        ]);
    }

    /**
     * Check my attendance (for wisudawan)
     */
    public function myPresensi(Request $request)
    {
        $user = $request->user();
        $wisudawan = $user->wisudawan;

        if (!$wisudawan) {
            return response()->json(['message' => 'Data wisudawan tidak ditemukan'], 404);
        }

        $presensi = $wisudawan->presensi;

        return response()->json([
            'presensi' => $presensi ? true : false,
            'waktu_scan' => $presensi ? $presensi->waktu_scan : null,
        ]);
    }
}
