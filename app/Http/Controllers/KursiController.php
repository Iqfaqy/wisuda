<?php

namespace App\Http\Controllers;

use App\Models\Kursi;
use App\Models\Wisudawan;
use Illuminate\Http\Request;

class KursiController extends Controller
{
    /**
     * Display all seats with stats
     */
    public function index(Request $request)
    {
        $hari = $request->get('hari', 1);
        
        $stats = [
            'total' => Kursi::count(),
            'terisi' => Kursi::occupied()->count(),
            'kosong' => Kursi::empty()->count(),
            'hari1_terisi' => Kursi::byHari(1)->occupied()->count(),
            'hari2_terisi' => Kursi::byHari(2)->occupied()->count(),
        ];

        // Section stats for both days
        $sectionStats = [];
        foreach (['A', 'B'] as $section) {
            $sectionStats[$section] = [
                'hari1' => Kursi::bySection($section)->byHari(1)->occupied()->count(),
                'hari2' => Kursi::bySection($section)->byHari(2)->occupied()->count(),
            ];
        }

        $kursi = Kursi::with('wisudawan.user')
            ->orderBy('section')
            ->orderBy('nomor')
            ->get()
            ->groupBy(['hari', 'section']);

        if ($request->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'sectionStats' => $sectionStats,
                'kursi' => $kursi,
            ]);
        }

        return view('admin.kursi.index', compact('kursi', 'stats', 'sectionStats'));
    }

    /**
     * Get seats by section and hari for layout display
     */
    public function getBySection(Request $request)
    {
        $section = $request->get('section', 'A');
        $hari = $request->get('hari', 1);

        $kursi = Kursi::with('wisudawan.user')
            ->bySection($section)
            ->byHari($hari)
            ->orderBy('nomor')
            ->get();

        return response()->json($kursi);
    }

    /**
     * Assign wisudawan to seat
     */
    public function assignSeat(Request $request)
    {
        $request->validate([
            'kursi_id' => 'required|exists:kursi,id',
            'wisudawan_id' => 'required|exists:wisudawan,id',
        ]);

        $kursi = Kursi::findOrFail($request->kursi_id);
        $wisudawan = Wisudawan::findOrFail($request->wisudawan_id);

        // Check if seat is already occupied
        if ($kursi->isOccupied()) {
            return response()->json([
                'message' => 'Kursi sudah terisi',
            ], 422);
        }

        // Check if wisudawan already has a seat
        $existingSeat = Kursi::where('wisudawan_id', $wisudawan->id)->first();
        if ($existingSeat) {
            return response()->json([
                'message' => 'Wisudawan sudah memiliki kursi: ' . $existingSeat->kode_kursi,
            ], 422);
        }

        // Validate gender match
        if (($kursi->jenis_kelamin === 'P' && $wisudawan->jenis_kelamin !== 'P') ||
            ($kursi->jenis_kelamin === 'L' && $wisudawan->jenis_kelamin !== 'L')) {
            return response()->json([
                'message' => 'Jenis kelamin tidak sesuai dengan section kursi',
            ], 422);
        }

        $kursi->update(['wisudawan_id' => $wisudawan->id]);

        return response()->json([
            'message' => 'Kursi berhasil di-assign',
            'kursi' => $kursi->load('wisudawan.user'),
        ]);
    }

    /**
     * Unassign wisudawan from seat
     */
    public function unassignSeat($id)
    {
        $kursi = Kursi::findOrFail($id);
        $kursi->update(['wisudawan_id' => null]);

        return response()->json([
            'message' => 'Kursi berhasil dikosongkan',
        ]);
    }

    /**
     * Search seat by code, NIM, or name
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $kursi = Kursi::with('wisudawan.user')
            ->where('kode_kursi', 'like', "%{$query}%")
            ->orWhereHas('wisudawan.user', function ($q) use ($query) {
                $q->where('nim', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json($kursi);
    }

    /**
     * Auto-arrange seats based on gender and prodi
     */
    public function autoArrange(Request $request)
    {
        $hari = $request->get('hari', 1);

        // Get all wisudawan without seats for this day
        $wisudawanWithoutSeats = Wisudawan::where('hari_wisuda', $hari)
            ->whereDoesntHave('kursi', function ($q) use ($hari) {
                $q->where('hari', $hari);
            })
            ->get();

        $assigned = 0;

        foreach ($wisudawanWithoutSeats as $w) {
            // Find empty seat matching gender
            $section = $w->jenis_kelamin === 'P' ? ['A'] : ['B'];
            
            $emptySeat = Kursi::whereIn('section', $section)
                ->where('hari', $hari)
                ->whereNull('wisudawan_id')
                ->orderBy('section')
                ->orderBy('nomor')
                ->first();

            if ($emptySeat) {
                $emptySeat->update(['wisudawan_id' => $w->id]);
                $assigned++;
            }
        }

        return response()->json([
            'message' => "Berhasil mengatur {$assigned} kursi",
            'assigned' => $assigned,
        ]);
    }

    /**
     * Get my seat (for wisudawan) - UPDATED FOR VIEW
     */
    public function mySeat(Request $request)
    {
        $user = $request->user();
        $wisudawan = $user->wisudawan;

        if (!$wisudawan) {
            // Redirect ke dashboard jika data wisudawan belum lengkap
            return redirect()->route('wisudawan.dashboard');
        }

        // 1. Ambil kursi milik user
        $myKursi = $wisudawan->kursi;

        // 2. Ambil semua kursi untuk menggambar denah visualisasi
        // Kita hanya mengambil kursi di "hari" yang sama dengan wisudawan
        $seatData = Kursi::where('hari', $wisudawan->hari_wisuda ?? 1)
            ->select('kode_kursi', 'section', 'nomor', 'jenis_kelamin', 'wisudawan_id')
            ->get()
            ->map(function($seat) {
                return [
                    'kode' => $seat->kode_kursi,
                    'section' => $seat->section,
                    'nomor' => $seat->nomor,
                    'gender' => $seat->jenis_kelamin,
                    'is_occupied' => !is_null($seat->wisudawan_id) // True jika ada penghuni
                ];
            });

        // 3. Return ke View Blade (BUKAN JSON)
        return view('wisudawan.kursi', [
            'user' => $user,
            'wisudawan' => $wisudawan,
            'myKursi' => $myKursi,
            'seatData' => $seatData
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_kursi' => 'required|unique:kursi,kode_kursi',
            'section' => 'required',
            'nomor' => 'required|integer',
            'hari' => 'required|integer|in:1,2',
            'jenis_kelamin' => 'required|in:L,P'
        ]);

        $kursi = Kursi::create($request->all());

        return response()->json([
            'message' => 'Kursi berhasil ditambahkan',
            'kursi' => $kursi
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kursi = Kursi::findOrFail($id);

        $request->validate([
            'kode_kursi' => 'required|unique:kursi,kode_kursi,' . $id,
            'section' => 'required',
            'nomor' => 'required|integer',
            'hari' => 'required|integer|in:1,2',
            'jenis_kelamin' => 'required|in:L,P'
        ]);

        $kursi->update($request->all());

        return response()->json([
            'message' => 'Kursi berhasil diupdate',
            'kursi' => $kursi
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kursi = Kursi::findOrFail($id);
        $kursi->delete();

        return response()->json(['message' => 'Kursi berhasil dihapus']);
    }
}
