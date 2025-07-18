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
    public function index(Request $request)
    {
        $status = $request->get('status', 'aktif'); // default 'aktif'
        $role = $request->get('role'); // bisa null
        
        $users = User::where('status', $status)
            ->when($role, function ($query, $role) {
                // Jika role disediakan di request, filter sesuai
                $query->where('role', $role);
            }, function ($query) {
                // Jika tidak ada role, tampilkan semua role kecuali magang dan mitra
                $query->whereNotIn('role', ['magang', 'mitra']);
            })
            ->orderBy('created_at')
            ->get();
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
            'mitra',
            'magang'
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
            'mitra',
            'magang'
        ];
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'nohp' => 'required|string|max:13',
            'id_telegram' => 'required|string|max:15',
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

    /**
     * Function Status Arsip dan Aktif view index.
     */
    public function toggleStatus(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->status = $request->status;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Status User berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah status barang.']);
        }
    }

    /*  */
    /* API CONTROLLER */
    /*  */
    public function apiUserUpdate(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'nohp' => 'required|string|max:13',
            'id_telegram' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Ambil user yang sedang login
        $user = auth()->user();

        // Update nama
        $user->name = $request->name;
        $user->nohp = $request->nohp;
        $user->id_telegram = $request->id_telegram;

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
            ->where('status', 'aktif')
            ->get();
        return response()->json($teknisi);
    }

    public function apiHelper($id) {
        $teknisi = User::where('role', 'magang')
            ->where('id', '!=', $id)
            ->where('status', 'aktif')
            ->get();
        return response()->json($teknisi);
    }
    public function apiUserAdmin() {
        $users = User::where('role', 'admin')
            ->orWhere('role', 'superadmin')->get();
        return response()->json($users);
    }
}
