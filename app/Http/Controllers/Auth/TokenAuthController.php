<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TokenAuthController extends Controller
{
    /**
     * SSO Callback: Menerima redirect dari Main-BAS dengan ?token=xxx
     */
    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            Log::warning('SSO (Eng): Callback tanpa token');
            return redirect()->route('login')->with('error', 'Token tidak ditemukan');
        }

        // 1. Verifikasi token ke Main-BAS (PULL VALIDATION)
        $mainBasUrl = env('MAIN_BAS_URL', 'http://localhost:8000');
        $secret = env('SSO_SECRET_KEY', 'BAS_SSO_SECRET_2025');

        try {
            Log::info('SSO (Eng): Mencoba verifikasi token', ['token_prefix' => substr($token, 0, 8)]);
            
            $response = Http::withHeaders([
                'X-SSO-Secret' => $secret,
            ])->post(rtrim($mainBasUrl, '/') . '/api/sso/verify', [
                'token' => $token,
            ]);

            if (!$response->successful()) {
                Log::error('SSO (Eng): Verifikasi token gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return redirect()->route('login')->with('error', 'Validasi SSO gagal');
            }

            $data = $response->json();
            
            if (!isset($data['success']) || !$data['success']) {
                return redirect()->route('login')->with('error', 'Token SSO tidak valid');
            }

            $userData = $data['user_data'];

            // 2. Cari atau buat user berdasarkan data dari Main-BAS
            // Gunakan username sebagai identifier utama karena dijamin unik di kedua sistem
            $user = User::where('username', $userData['username'])->first();

            if (!$user) {
                // Auto-create user jika belum ada
                $user = User::create([
                    'username' => $userData['username'],
                    'email' => $userData['email'] ?? null,
                    'nik' => $userData['nik'] ?? null,
                    'jabatan' => $userData['jabatan'] ?? null,
                    'departemen' => $userData['departemen'] ?? null,
                    'bagian' => $userData['bagian'] ?? null,
                    'password' => Hash::make(Str::random(32)),
                ]);
            } else {
                // Update data user agar sinkron
                $user->update([
                    'email' => $userData['email'] ?? $user->email,
                    'nik' => $userData['nik'] ?? $user->nik,
                    'jabatan' => $userData['jabatan'] ?? $user->jabatan,
                    'departemen' => $userData['departemen'] ?? $user->departemen,
                    'bagian' => $userData['bagian'] ?? $user->bagian,
                ]);
            }

            // 3. Login user
            Auth::login($user);
            $request->session()->regenerate();

            Log::info("SSO (Eng): Login sukses untuk user [{$user->email}] via SSO");

            return redirect()->route('dashboard')
                ->with('success', 'Login berhasil melalui SSO');

        } catch (\Exception $e) {
            Log::error('SSO (Eng): Error koneksi ke Main-BAS', [
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('login')->with('error', 'Komunikasi SSO bermasalah');
        }
    }
}
