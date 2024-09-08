<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelians = Pembelian::with('barang')->get();
        return view('pembelian.index_pembelian', compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::all();
        return view('pembelian.create_pembelian', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'jumlah' => 'required|integer',
            'harga_beli' => 'required|numeric',
            'tanggal_pembelian' => 'required|date',
        ]);

        try {
            // Menambah stok barang
            $barang = Barang::find($request->barang_id);
            $barang->stok += $request->jumlah;

            // Mengubah harga beli
            $barang->harga_beli = $request->harga_beli/$request->jumlah;
            $barang->save();

            // Menyimpan data pembelian
            Pembelian::create($request->all());
            
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan dan stok barang diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.create')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $pembelian = Pembelian::findOrFail($id);
        $barang = Barang::all();
        return view('pembelian.edit_pembelian', compact('pembelian', 'barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric',
            'tanggal_pembelian' => 'required|date',
        ]);
        
        try {
            // Temukan data pembelian berdasarkan ID
            $pembelian = Pembelian::findOrFail($id);

            // Simpan jumlah lama sebelum pembaruan
            $oldJumlah = $pembelian->jumlah;

            // Temukan barang berdasarkan ID
            $barang = Barang::findOrFail($request->barang_id);

            // Hitung stok baru
            $newJumlah = $request->jumlah;
            $stokBaru = $barang->stok - $oldJumlah + $newJumlah;

            // Cek apakah stok tidak menjadi negatif
            if ($stokBaru < 0) {
                return redirect()->back()->withErrors(['msg' => 'Stok tidak bisa kurang dari 0.']);
            }

            // Update stok barang
            $barang->stok = $stokBaru;
            $barang->save();

            // Update data pembelian dengan field tertentu
            $pembelian->update([
                'barang_id' => $request->barang_id,
                'jumlah' => $newJumlah,
                'harga_beli' => $request->harga_beli,
                'tanggal_pembelian' => $request->tanggal_pembelian,
            ]);

            return redirect()->route('pembelian.index')->with('success', 'Barang masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('pembelian.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        // Kurangi stok barang sebelum menghapus
        $barang = Barang::find($pembelian->barang_id);
        $barang->stok -= $pembelian->jumlah;
        $barang->save();

        $pembelian->delete();

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus dan stok barang diperbarui.');
    }
}
