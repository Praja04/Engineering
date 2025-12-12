<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

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
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
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

            $intendedUrl = session()->get('url.intended', $this->redirectByJabatan($user->jabatan));

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'redirect' => $intendedUrl
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

    public function manage_user()
    {
        return view('user.manage_user');
    }

    // Menyediakan data untuk DataTables
    public function getUsers()
    {
        $users = User::select('id', 'username', 'jabatan', 'email', 'nik', 'image', 'created_at', 'departemen', 'bagian')->get();

        return response()->json($users);
    }

    // Menyimpan data pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'email' => 'required|email',
            'jabatan' => 'required',
            'nik' => 'required',
            'departemen' => 'required',
            'bagian' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
        }

        User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'nik' => $request->nik,
            'jabatan' => $request->jabatan,
            'departemen' => $request->departemen,
            'bagian' => $request->bagian,
            'image' => $imageName,
        ]);

        return response()->json(['success' => 'User created successfully.']);
    }

    // Menampilkan data pengguna untuk di-edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Mengupdate data pengguna
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email',
            'jabatan' => 'required',
            'nik' => 'required',
            'departemen' => 'required',
            'bagian' => 'required',
            'password' => 'nullable|min:6',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $user = User::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
                unlink(public_path('uploads/users/' . $user->image));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $user->image = $imageName;
        }

        // Update data user
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'nik' => $request->nik,
            'departemen' => $request->departemen,
            'bagian' => $request->bagian,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        return response()->json(['success' => 'User updated successfully.']);
    }

    // Menghapus data pengguna
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }
        $user->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }

    public function getTotalUsers()
    {
        // Mengambil jumlah total user
        $totalUsers = User::count();

        // Mengembalikan response dalam bentuk JSON
        return response()->json([
            'success' => true,
            'total_users' => $totalUsers,
        ]);
    }
}
