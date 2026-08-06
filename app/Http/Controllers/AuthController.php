<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek kredensial login
        if (Auth::attempt($credentials)) {
            // Regenerasi session
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog');
            }

            // Jika role tidak dikenal, logout
            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenal.');
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}