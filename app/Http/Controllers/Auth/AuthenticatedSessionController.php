<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak terdaftar.',
            ])->onlyInput('email');
        }

        // Cek status user
        if ($user->status !== 'AKTIF') {
            return back()->withErrors([
                'email' => 'Akun Anda belum diaktifkan. Silakan hubungi admin.',
            ])->onlyInput('email');
        }

        // ✅ PERBAIKAN: Ambil nilai remember dari checkbox
        // Jika checkbox tidak dicentang, $remember = false → session hilang saat browser ditutup
        // Jika checkbox dicentang, $remember = true → session persist dengan cookie
        $remember = $request->boolean('remember');

        // Attempt login dengan remember me
        if (!Auth::attempt($request->only('email', 'password'), $remember)) {
            return back()->withErrors([
                'email' => 'Password yang Anda masukkan salah.',
            ])->onlyInput('email');
        }

        // Regenerate session untuk keamanan (mencegah session fixation)
        $request->session()->regenerate();

        // Log aktivitas login
        \App\Models\AuditTrail::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'module' => 'AUTH',
            'new_values' => json_encode([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'remember_me' => $remember,
            ]),
            'ip_address' => $request->ip(),
        ]);

        // Redirect berdasarkan role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Redirect user berdasarkan role
     */
    private function redirectBasedOnRole($user): RedirectResponse
    {
        $role = $user->role;

        if ($role === 'OWNER') {
            return redirect()->intended(route('dashboard.owner'));
        }

        // ✅ SUPERVISOR & STAFF_GUDANG → Dashboard Gudang
        if (in_array($role, ['SUPERVISOR', 'STAFF_GUDANG'])) {
            return redirect()->intended(route('dashboard.gudang'));
        }

        if (in_array($role, ['SUBLIM', 'KONVEKSI'])) {
            return redirect()->intended(route('po.index'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Log aktivitas logout
        if ($user) {
            \App\Models\AuditTrail::create([
                'user_id' => $user->id,
                'action' => 'LOGOUT',
                'module' => 'AUTH',
                'new_values' => json_encode([
                    'ip' => $request->ip(),
                ]),
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
