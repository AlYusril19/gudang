<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::all();
        return view('barang.index_kategori_barang', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('barang.create_kategori_barang');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:64|unique:kategori',
            'satuan' => 'nullable|string|max:32',
        ]);

        Kategori::create($request->all());

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil didaftarkan.');
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
        $kategori = Kategori::findOrFail($id);
        return view('barang.edit_kategori_barang', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:64|unique:kategori,nama_kategori,' . $id,
            'satuan' => 'nullable|string|max:32',
        ]);
        $kategori = Kategori::findOrFail($id);

        try {
            $kategori->update($request->all());
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diedit');
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategoris = Kategori::findOrFail($id);
    
        // Cek apakah kategori sedang digunakan oleh barang
        if ($kategoris->barang()->exists()) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena sedang digunakan oleh barang.');
        }
        
        // Jika tidak digunakan, hapus kategori
        $kategoris->delete();
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
