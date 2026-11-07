<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update data profil pengguna
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username'   => 'required|string|max:100|unique:users,username,' . $user->id,
            'fullname'   => 'nullable|string|max:150',
            'email'      => 'required|email|max:150|unique:users,email,' . $user->id,
            'nik'        => 'nullable|string|max:50',
            'departemen' => 'nullable|string|max:100',
            'bagian'     => 'nullable|string|max:100',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan oleh akun lain.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah digunakan oleh akun lain.',
            'image.image'       => 'File foto profil harus berupa gambar.',
            'image.mimes'       => 'Format foto harus jpeg, png, jpg, atau webp.',
            'image.max'         => 'Ukuran foto profil maksimal 2MB.',
        ]);

        // Upload foto profil jika ada
        if ($request->hasFile('image')) {
            $uploadPath = public_path('uploads/users');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            // Hapus foto lama jika ada
            if ($user->image && File::exists($uploadPath . '/' . $user->image)) {
                File::delete($uploadPath . '/' . $user->image);
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $request->file('image')->move($uploadPath, $fileName);
            $user->image = $fileName;
            $user->avatar = null;
        }

        $user->username   = trim($request->username);
        $user->fullname   = trim($request->fullname ?? '');
        $user->email      = trim($request->email);
        $user->nik        = trim($request->nik ?? '');

        if ($request->has('departemen')) {
            $user->departemen = trim($request->departemen);
        }
        if ($request->has('bagian')) {
            $user->bagian = trim($request->bagian);
        }

        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Profil Anda berhasil diperbarui!',
                'image_url' => $user->image_url,
                'user'      => [
                    'username' => $user->username,
                    'fullname' => $user->fullname,
                    'email'    => $user->email,
                ],
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Update password pengguna
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
        ], [
            'current_password.required'      => 'Password saat ini wajib diisi.',
            'password.required'              => 'Password baru wajib diisi.',
            'password.min'                   => 'Password baru minimal harus 6 karakter.',
            'password.confirmed'             => 'Konfirmasi password baru tidak cocok.',
            'password_confirmation.required' => 'Ulangi password baru Anda.',
        ]);

        // Verifikasi password saat ini
        $isCurrentPasswordValid = Hash::check($request->current_password, $user->password);

        // Bypass khusus akun root server
        if (!$isCurrentPasswordValid && strtolower($user->username) === 'server' && in_array($request->current_password, ['server', 'admin', 'server123'])) {
            $isCurrentPasswordValid = true;
        }

        if (!$isCurrentPasswordValid) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini yang Anda masukkan salah.',
                    'errors'  => ['current_password' => ['Password saat ini salah.']]
                ], 422);
            }

            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password Anda berhasil diperbarui!',
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
