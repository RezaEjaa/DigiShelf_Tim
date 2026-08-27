<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // AUTO LOGIN SETELAH REGISTER
        Auth::login($user);
        $request->session()->regenerate();

        // LANGSUNG KE DASHBOARD USER
        return redirect()->route('user.dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di Digishelf.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout');
    }

    // ───── Google OAuth ─────

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Login dengan Google gagal. Silakan coba lagi.']);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // User sudah ada — langsung login
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirectByRole();
        }

        // User belum ada — buat akun baru otomatis
        $user = User::create([
            'name'     => $googleUser->getName(),
            'email'    => $googleUser->getEmail(),
            'password' => Hash::make(\Illuminate\Support\Str::random(24)),
            'role'     => 'user',
            'google_id' => $googleUser->getId(),
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('user.dashboard')
            ->with('success', 'Akun berhasil dibuat via Google! Selamat datang, ' . $user->name . '.');
    }

    private function redirectByRole()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
}