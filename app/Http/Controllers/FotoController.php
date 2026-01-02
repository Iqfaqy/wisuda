<?php

namespace App\Http\Controllers;

use App\Models\FotoWisuda;
use Illuminate\Http\Request;

class FotoController extends Controller
{
    /**
     * Display a listing of foto wisuda links
     */
    public function index(Request $request)
    {
        $fotos = FotoWisuda::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($fotos);
        }

        return view('admin.foto.index', compact('fotos'));
    }

    /**
     * Store a new foto/drive link
     */
    public function store(Request $request)
    {
        $request->validate([
            'drive_link' => 'required|url|max:500',
            'hari' => 'required|in:1,2',
            'deskripsi' => 'nullable|string',
        ]);

        $foto = FotoWisuda::create([
            'drive_link' => $request->drive_link,
            'hari' => $request->hari,
            'deskripsi' => $request->deskripsi,
            'created_by' => $request->user()->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Link foto berhasil ditambahkan',
                'foto' => $foto,
            ], 201);
        }

        return redirect()->back()->with('success', 'Link foto berhasil disimpan');
    }

    /**
     * Update foto link
     */
    public function update(Request $request, $id)
    {
        $foto = FotoWisuda::findOrFail($id);

        $request->validate([
            'drive_link' => 'sometimes|url|max:500',
            'hari' => 'sometimes|in:1,2',
            'deskripsi' => 'nullable|string',
        ]);

        $foto->update($request->only(['drive_link', 'hari', 'deskripsi']));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Link foto berhasil diupdate',
                'foto' => $foto,
            ]);
        }

        return redirect()->back()->with('success', 'Link foto berhasil diperbarui');
    }

    /**
     * Remove the specified foto link
     */
    public function destroy(Request $request, $id)
    {
        $foto = FotoWisuda::findOrFail($id);
        $foto->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Link foto berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Link foto berhasil dihapus');
    }

    /**
     * Get all foto links for wisudawan view
     * PERBAIKAN: Return View, bukan JSON
     */
    public function getAll(Request $request)
    {
        $user = $request->user();
        
        // Ambil data foto dari database urut terbaru
        $fotos = FotoWisuda::orderBy('created_at', 'desc')->get();

        return view('wisudawan.foto', compact('user', 'fotos'));
    }
}
