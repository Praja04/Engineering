<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    private function redirectByJabatan($jabatan)
    {
        return match ($jabatan) {
            'dept_head'  => route('dashboard'),
            'foreman'    => route('dashboard'),
            'supervisor' => route('dashboard'),
            'operator'   => route('dashboard'),
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

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            Auth::login($user);
            $request->session()->regenerate();
            // $user = Auth::user();

            // $imageUrl = $user->image && url(Storage::disk('public')->exists($user->image))
            //     ? url(Storage::url($user->image)) // -> /storage/...
            //     : asset('material/assets/images/users/user-dummy-img.jpg');

            // Simpan data tambahan ke session
            // session([
            //     'username'   => $user->username,
            //     'jabatan'    => $user->jabatan,
            //     'departemen' => $user->departemen,
            //     'bagian'     => $user->bagian,
            //     'nik'        => $user->nik,
            //     'image_url'  => $imageUrl,
            //     'status'     => $user->status,
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'redirect' => $this->redirectByJabatan($user->jabatan)
            ]);
        }
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
