<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wisudawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.akun.index', compact('users'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|unique:users,nim',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,wisudawan',
        ]);

        $user = User::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'aktif',
        ]);

        // If wisudawan, create wisudawan profile
        if ($request->role === 'wisudawan') {
            Wisudawan::create([
                'user_id' => $user->id,
                'prodi' => $request->prodi ?? null,
                'fakultas' => $request->fakultas ?? null,
                'jenis_kelamin' => $request->jenis_kelamin ?? null,
                'telepon' => $request->telepon ?? null,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User berhasil ditambahkan',
                'user' => $user,
            ], 201);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        // Ambil data user beserta relasi wisudawan (jika ada)
        $user = User::with('wisudawan')->findOrFail($id);

        // Return ke View Edit, BUKAN JSON
        return view('admin.akun.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nim' => 'sometimes|string|unique:users,nim,' . $id,
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:admin,wisudawan',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $data = $request->only(['nim', 'name', 'email', 'role', 'status']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User berhasil diupdate',
                'user' => $user,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'User berhasil dihapus']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Default password: tanggal lahir format DDMMYYYY or simple default
        $defaultPassword = '12345678';
        
        $user->update([
            'password' => Hash::make($defaultPassword),
        ]);

        return response()->json([
            'message' => 'Password berhasil direset',
            'new_password' => $defaultPassword,
        ]);
    }

    /**
     * Get account statistics
     */
    public function getStats()
    {
        return response()->json([
            'total' => User::count(),
            'aktif' => User::where('status', 'aktif')->count(),
            'nonaktif' => User::where('status', 'nonaktif')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'wisudawan' => User::where('role', 'wisudawan')->count(),
        ]);
    }
}
