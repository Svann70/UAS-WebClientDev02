<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        // Validasi server-side: satu-satunya gerbang sebelum data masuk database.
        // `confirmed` butuh field password_confirmation; `Password::min(8)` paksa panjang minimum.
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:customer,merchant'], // admin TIDAK boleh daftar sendiri
        ]);

        // Role diambil dari input terbatas (customer/merchant) saja, bukan bebas.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'], // cast 'hashed' otomatis bcrypt
        ]);
        $user->role = $data['role'];
        $user->save();

        Auth::login($user);
        $request->session()->regenerate(); // cegah session fixation

        return redirect()->route($user->isMerchant() ? 'merchant.dashboard' : 'home')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, ' . $user->name . '!');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Auth::attempt otomatis pakai hash check + parameterized query.
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            // Pesan sengaja umum: jangan bocorkan apakah email terdaftar atau tidak (user enumeration).
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate(); // cegah session fixation

        $user = $request->user();
        return redirect()->intended(match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isMerchant() => route('merchant.dashboard'),
            default => route('home'),
        });
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();      // buang data sesi
        $request->session()->regenerateToken(); // token CSRF baru

        return redirect()->route('home')->with('success', 'Anda telah keluar.');
    }
}