<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    private function redirectByJabatan($jabatan)
    {
        return match ($jabatan) {
            'dept_head'  => route('dept_head.dashboard'),
            'foreman'    => route('foreman.dashboard'),
            'supervisor' => route('supervisor.dashboard'),
            'operator'   => route('operator.dashboard'),
            default      => route('home'),
        };
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('nik', $request->username)
            ->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'Username atau password salah',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Simpan data tambahan ke session
        session([
            'username'   => $user->username,
            'jabatan'    => $user->jabatan,
            'departemen' => $user->departemen,
            'bagian'     => $user->bagian,
            'nik'        => $user->nik,
            'image'      => $user->image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'redirect' => $this->redirectByJabatan($user->jabatan)
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush(); // Bersihkan semua session
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
