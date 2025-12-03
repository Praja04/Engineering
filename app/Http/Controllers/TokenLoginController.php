<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class TokenLoginController extends Controller
{
    public function receiveToken(Request $request)
    {
        // validasi data dari portal utama
        $request->validate([
            'token' => 'required|string',
            'user_id' => 'required|integer',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        // cek user di portal tujuan
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // kalau belum ada, bisa auto-register
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(16)),
            ]);
        }

        // login-kan user
        Auth::login($user);

        // redirect URL setelah login (bisa ke dashboard portal ini)
        $redirectUrl = url('/utility/dashboard?token=' . $request->token);

        return response()->json([
            'status' => 'success',
            'redirect_url' => $redirectUrl,
        ]);
    }
}
