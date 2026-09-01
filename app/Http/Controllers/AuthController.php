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

    private $redirects = [
        'dept_head' => '/home',
        'supervisor' => '/home',
        'foreman' => '/home',
        'operator' => '/home',
    ];

    public function login(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'success' => true,
                'message' => 'Anda sudah login.',
                'redirect' => $this->redirectUser(Auth::user()),
            ]);
        }

        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // $user = User::where('username', $request->username)
        //     ->orWhere('nik', $request->username)
        //     ->first();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Username atau password salah'
        //     ], 401);
        // }

        $credentials = $request->only('username', 'password');

        // Khusus Akun Root Server (Support login bypass password default)
        if (strtolower($request->username) === 'server' && in_array($request->password, ['server', 'server123', 'admin', 'admin123', '123456'])) {
            $serverUser = User::whereRaw('LOWER(username) = "server"')->first();
            if ($serverUser) {
                Auth::login($serverUser);
                $request->session()->regenerate();
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil sebagai Server Root.',
                    'redirect' => url('/ejo-engineer'),
                ]);
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
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

            // $intendedUrl = session()->get('url.intended', $this->redirectByJabatan($user->jabatan));

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'redirect' => $this->redirectUser($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login gagal. Periksa username atau password Anda.',
        ], 401);
    }

    private function redirectUser($user)
    {
        // $jabatan = strtolower($user->jabatan);

        // $path = $this->redirects[$jabatan] ?? '/';

        // return url($path);
        $intended = redirect()->intended()->getTargetUrl();

        if ($intended !== url('/')) {  // kalau bukan default '/', berarti ada intended
            return $intended;
        }

        // Kalau tidak ada intended, fallback ke jabatan
        $jabatan = strtolower($user->jabatan ?? '');

        $path = $this->redirects[$jabatan] ?? '/home';

        return url($path);
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
