<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wisudawan;
use App\Models\Kursi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class WisudawanController extends Controller
{
    /**
     * Display a listing of wisudawan
     */
    public function index(Request $request)
    {
        $query = Wisudawan::with('user', 'kursi', 'presensi');

        // Filter by prodi
        if ($request->has('prodi') && $request->prodi != '') {
            $query->where('prodi', $request->prodi);
        }

        // Filter by hari wisuda
        if ($request->has('hari') && $request->hari != '') {
            $query->where('hari_wisuda', $request->hari);
        }

        // Filter by jenis kelamin
        if ($request->has('jenis_kelamin') && $request->jenis_kelamin != '') {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $wisudawan = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($wisudawan);
        }

        return view('admin.wisudawan.index', compact('wisudawan'));
    }

    /**
     * Store a newly created wisudawan AND User account
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            // Data User
            'nim' => 'required|unique:users,nim',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // TAMBAHKAN VALIDASI PASSWORD INI
            
            // Data Wisudawan
            'prodi' => 'required|string',
            'fakultas' => 'nullable|string',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'jenis_kelamin' => 'required|in:L,P',
            'telepon' => 'nullable|string',
            'nama_ortu' => 'nullable|string',
            'jumlah_tamu' => 'nullable|integer|min:0',
            'hari_wisuda' => 'nullable|in:1,2',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                
                // A. Buat User Baru
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'nim' => $request->nim,
                    // UBAH BAGIAN INI: Gunakan password dari request
                    'password' => Hash::make($request->password), 
                    'role' => 'wisudawan',
                ]);

                // B. Buat Data Wisudawan
                $wisudawan = Wisudawan::create([
                    'user_id' => $user->id,
                    'prodi' => $request->prodi,
                    'fakultas' => $request->fakultas,
                    'ipk' => $request->ipk,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'telepon' => $request->telepon,
                    'nama_ortu' => $request->nama_ortu,
                    'jumlah_tamu' => $request->jumlah_tamu ?? 0,
                    'hari_wisuda' => $request->hari_wisuda ?? 1,
                ]);

                if ($wisudawan->ipk && method_exists($wisudawan, 'calculatePredikat')) {
                    $wisudawan->update(['predikat' => $wisudawan->calculatePredikat()]);
                }

                return $wisudawan->load('user');
            });

            return response()->json([
                'message' => 'Wisudawan berhasil ditambahkan dengan password manual.',
                'wisudawan' => $result,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified wisudawan
     */
    public function show($id)
    {
        $wisudawan = Wisudawan::with('user', 'kursi', 'presensi')->findOrFail($id);

        return response()->json($wisudawan);
    }

    /**
     * Update the specified wisudawan
     */
    public function update(Request $request, $id)
    {
        $wisudawan = Wisudawan::findOrFail($id);

        $request->validate([
            'prodi' => 'nullable|string',
            'fakultas' => 'nullable|string',
            'ipk' => 'nullable|numeric|between:0,4',
            'jenis_kelamin' => 'nullable|in:L,P',
            'telepon' => 'nullable|string',
            'nama_ortu' => 'nullable|string',
            'jumlah_tamu' => 'nullable|integer|min:0',
            'hari_wisuda' => 'nullable|in:1,2',
        ]);

        $wisudawan->update($request->all());

        // Recalculate predikat if IPK changed
        if ($request->has('ipk') && $wisudawan->ipk) {
            if (method_exists($wisudawan, 'calculatePredikat')) {
                $wisudawan->update(['predikat' => $wisudawan->calculatePredikat()]);
            }
        }

        return response()->json([
            'message' => 'Data wisudawan berhasil diupdate',
            'wisudawan' => $wisudawan->load('user'),
        ]);
    }

    /**
     * Remove the specified wisudawan
     */
    public function destroy($id)
    {
        // Hapus Wisudawan & User-nya juga (Opsional, tergantung kebijakan)
        // Jika ingin user tetap ada, hapus bagian user->delete()
        
        $wisudawan = Wisudawan::findOrFail($id);
        $user = User::find($wisudawan->user_id);
        
        DB::transaction(function () use ($wisudawan, $user) {
            $wisudawan->delete();
            if ($user) {
                $user->delete(); // Hapus akun login juga
            }
        });

        return response()->json(['message' => 'Data wisudawan dan akun berhasil dihapus']);
    }

    /**
     * Get current user's profile (for wisudawan)
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Pastikan relation wisudawan dimuat
        $wisudawan = $user->wisudawan;

        if (!$wisudawan) {
             // Jika data tidak ada, bisa redirect atau tampilkan error
            return redirect()->back()->with('error', 'Data wisudawan tidak ditemukan');
        }

        // PERBAIKAN: Return ke View, bukan JSON
        return view('wisudawan.profile', [
            'user' => $user,
            'wisudawan' => $wisudawan->load('kursi', 'presensi'),
        ]);
    }

    /**
     * Get list of prodi for filtering
     */
    public function getProdiList()
    {
        $prodiList = Wisudawan::select('prodi')
            ->distinct()
            ->whereNotNull('prodi')
            ->orderBy('prodi')
            ->pluck('prodi');

        return response()->json($prodiList);
    }

    /**
     * Menampilkan halaman Info Kursi untuk Wisudawan
     */
    public function kursi(Request $request)
    {
        $user = $request->user();
        $wisudawan = $user->wisudawan;

        // 1. Ambil Kursi milik user ini (jika ada)
        $myKursi = $wisudawan->kursi;

        // 2. Ambil Semua Kursi di hari yang sama untuk denah (Visualisasi)
        // Kita ambil kode_kursi, section, dan status terisi (wisudawan_id != null)
        $hari = $wisudawan->hari_wisuda ?? 1;
        
        $allSeats = \App\Models\Kursi::where('hari', $hari)
            ->select('id', 'kode_kursi', 'section', 'nomor', 'jenis_kelamin', 'wisudawan_id')
            ->get()
            ->map(function($seat) {
                return [
                    'kode' => $seat->kode_kursi,
                    'section' => $seat->section,
                    'nomor' => $seat->nomor,
                    'gender' => $seat->jenis_kelamin, // L atau P
                    'is_occupied' => !is_null($seat->wisudawan_id) // True jika ada penghuninya
                ];
            });

        // PERBAIKAN: Return ke View Blade, bukan JSON
        return view('wisudawan.kursi', [
            'user' => $user,
            'wisudawan' => $wisudawan,
            'myKursi' => $myKursi,
            'seatData' => $allSeats // Data untuk peta kursi di JS
        ]);
    }

    /**
     * Update profile data by Wisudawan (Self-Service)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $wisudawan = $user->wisudawan;

        if (!$wisudawan) {
            return redirect()->back()->with('error', 'Data Wisudawan tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'telepon' => 'required|string|max:20',
            'nama_ortu' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'judul_skripsi' => 'required|string',
            'ukuran_toga' => 'required|in:S,M,L,XL,XXL',
        ]);

        // Update Data
        $wisudawan->update([
            'telepon' => $request->telepon,
            'nama_ortu' => $request->nama_ortu,
            'nama_ibu' => $request->nama_ibu,
            'judul_skripsi' => $request->judul_skripsi,
            'ukuran_toga' => $request->ukuran_toga,
        ]);

        return redirect()->back()->with('success', 'Data profil berhasil diperbarui.');
    }
}