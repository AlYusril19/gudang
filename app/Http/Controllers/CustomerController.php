<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customer_index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'mitra')->get();
        return view('admin.customer_create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:64|unique:customers',
            'hp' => 'nullable',
            'mitra_id' => 'nullable|exists:users,id'
        ]);

        Customer::create($request->all());

        return redirect()->route('customer.index')->with('success', 'Customer berhasil didaftarkan.');
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
        $customer = Customer::findOrFail($id);
        $users = User::where('role', 'mitra')->get();
        return view('admin.customer_edit', compact('users', 'customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:64|unique:customers,nama,' . $id, // Mengabaikan ID yang sedang diupdate
            'hp' => 'nullable',
            'mitra_id' => 'nullable|exists:users,id' // Pastikan mitra_id ada di tabel users
        ]);

        // Temukan customer berdasarkan ID
        $customer = Customer::findOrFail($id);

        // Update data customer
        $customer->update($request->all());

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $customers = Customer::findOrFail($id);
        if ($user->role != 'superadmin') {
            return redirect()->back()->with('error', 'Hanya superadmin yang dapat menghapus data ini');
        }
        $customers->delete();

        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus');
    }

    // 
    // DATA API
    // 
    public function getCustomers(Request $request) {
        $customers = Customer::all();
        return response()->json($customers);
    }
}
