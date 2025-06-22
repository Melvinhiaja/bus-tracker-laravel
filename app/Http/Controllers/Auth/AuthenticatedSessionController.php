<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login user
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validasi login menggunakan username dan password
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba autentikasi user
        if (Auth::attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();

            // Update status menjadi aktif
            Auth::user()->update(['is_active' => true]);

            // Redirect berdasarkan role
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'karyawan') {
                return redirect()->route('karyawan.dashboard');
            } elseif ($user->role === 'driver') {
                return redirect()->route('driver.index');
            } else {
                return redirect()->route('unauthorized');
            }
        }

        // Jika gagal login, kirimkan pesan error
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    /**
     * Logout user
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Set status menjadi tidak aktif saat logout
        if (Auth::check()) {
            Auth::user()->update(['is_active' => false]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
