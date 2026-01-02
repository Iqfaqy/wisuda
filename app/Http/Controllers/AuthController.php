<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Process login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nim', $request->nim)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nim' => ['NIM atau password salah.'],
            ]);
        }

        if ($user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'nim' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        Auth::login($user, $request->boolean('remember'));

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/wisudawan/dashboard');
    }

    /**
     * API Login (for AJAX/SPA)
     */
    public function apiLogin(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nim', $request->nim)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'NIM atau password salah.'], 401);
        }

        if ($user->status !== 'aktif') {
            return response()->json(['message' => 'Akun tidak aktif.'], 403);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        // Create API token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load wisudawan data if exists
        $userData = $user->toArray();
        if ($user->wisudawan) {
            $userData = array_merge($userData, $user->wisudawan->toArray());
            if ($user->wisudawan->kursi) {
                $userData['kursi'] = $user->wisudawan->kursi->kode_kursi;
            }
            $userData['presensi'] = $user->wisudawan->presensi ? true : false;
        }

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $userData,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * API Logout
     */
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
