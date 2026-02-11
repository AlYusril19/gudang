<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'aktif');
        $role   = $request->get('role');

        $users = User::query()
            ->where('status', $status);

        if ($role) {
            // Jika role dipilih
            $users->where('role', $role);
        }
        elseif ($status === 'arsip') {
            $users->where('status', 'arsip');
        }
        else {
            // Default: selain magang & mitra
            $users->whereNotIn('role', ['magang', 'mitra']);
        }

        $users = $users->orderBy('created_at', 'desc')->get();

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
        $request->validate([
            'name'          => 'nullable|string|max:64',
            'nohp'          => 'nullable|string|max:13',
            'id_telegram'   => 'nullable|string|max:15',
            'password'      => 'nullable|string|min:8|confirmed',
            'alamat'        => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user->fill($request->only([
            'name',
            'nohp',
            'id_telegram',
            'alamat',
            'tanggal_lahir'
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!'
        ]);
    }


    public function apiUserProfilUpdate(Request $request) 
    {
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();   // alias Auth::user()

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('photos', 'public');

            $user->photo = $path;
        }

        $user->save();

        return response()->json([
            'message'   => 'Foto profil berhasil diperbarui!',
            'photo_url' => $user->photo ? Storage::url($user->photo) : null,
        ], 200);
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
    public function apiUsername($name) {
        $user = User::where('name', $name)->first();
        return response()->json($user);
    }
}
