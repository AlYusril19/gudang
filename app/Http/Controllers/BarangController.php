<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderBy = $request->get('order_by', 'nama_barang');  // Default sorting by nama_barang
        $direction = $request->get('direction', 'asc');       // Default direction is ascending

        $search = $request->get('search');  // Ambil parameter pencarian

        // Query dengan pencarian dan pengurutan
        $barangs = Barang::when($search, function ($query, $search) {
                return $query->where('nama_barang', 'like', "%{$search}%");
            })
            ->orderBy($orderBy, $direction)
            ->get();

        return view('barang.index_barang', compact('barangs', 'orderBy', 'direction', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('barang.create_barang');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->deskripsi);
        $request->validate([
            // 'kode_barang' => 'required|string|max:32|unique:barang',
            'nama_barang' => 'required|string|max:32|unique:barang',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'deskripsi' => 'required|string|max:1024',
        ]);
        $hargaJual = (1 + ($request->harga_jual/100)) * $request->harga_beli;

        $kodeBarang = Barang::latest('id')->value('id');
        $kodeBarang = $kodeBarang ? $kodeBarang + 1 : 1;
        $kodeBarang = "K-" . str_pad($kodeBarang, 3, '0', STR_PAD_LEFT);

        Barang::create([
            'kode_barang' => $kodeBarang,
            'nama_barang' => $request->nama_barang,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $hargaJual,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.show_barang', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit_barang', compact('barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:32|unique:barang,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:32|unique:barang',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'deskripsi' => 'required|string|max:1024',
        ]);
        
        try {
            $barang = Barang::findOrFail($id);
            
            $hargaJual = (1 + ($request->harga_jual/100)) * $request->harga_beli;
            
            $barang->update([
                'kode_barang' => $request->kode_barang,
                'nama_barang' => $request->nama_barang,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $hargaJual,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('barang.show', $barang->id )->with('success', 'Peserta berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('barang.edit')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
}
