<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10); // Menggunakan pagination untuk menampilkan 10 pengguna per halaman
        return view('admin.users-index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allowedRoleUser = [
            'admin',
            'operator',
            'staff',
            'mitra'
        ];
        return view('admin.user-create', compact('allowedRoleUser')); // Buat view untuk form tambah user
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $allowedRoleUser = [
            'admin',
            'operator',
            'staff',
            'mitra'
        ];
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoleUser)],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $users = auth()->user();
        if ($users->role != 'superadmin') {
            return redirect()->back()->with('error', 'Hanya superadmin yang dapat menghapus data ini');
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    /*  */
    /* API CONTROLLER */
    /*  */
    public function apiUserUpdate(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Update nama
        $user->name = $request->name;

        // Jika password diisi, baru update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Simpan perubahan
        $user->save();

        return response()->json(['message' => 'Profile updated successfully!'], 200);
    }

    public function apiUser($id) {
        return response()->json(User::findOrFail($id));
    }

    public function apiTeknisi($id) {
        $teknisi = User::where('role', 'staff')
            ->where('id', '!=', $id)
            ->get();
        return response()->json($teknisi);
    }
}
