<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualans = Penjualan::with('barang')->get();
        return view('penjualan.index_penjualan', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::all();
        // dd($barang);
        return view('penjualan.create_penjualan', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_penjualan' => 'required|date',
        ]);

        try {
            // Ambil data barang berdasarkan ID yang dipilih
            $barang = Barang::findOrFail($request->barang_id);
            
            // Hitung total harga
            $totalHarga = $barang->harga_jual * $request->jumlah;
            
            // stok barang berkurang
            if ($barang->stok < $request->jumlah) {
                return redirect()->back()->withErrors(['msg' => 'Stok barang tidak mencukupi.']);
                // return redirect()->route('penjualan.create')->with('error', 'Stok Barang Kurang.');
            }
            $barang->stok -= $request->jumlah;
            $barang->save();

            // Buat penjualan baru
            Penjualan::create([
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_jual' => $totalHarga,
                'tanggal_penjualan' => $request->tanggal_penjualan,
            ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('penjualan.create')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        $penjualan = Penjualan::findOrFail($id);
        // Kurangi stok barang sebelum menghapus
        $barang = Barang::find($penjualan->barang_id);
        $barang->stok += $penjualan->jumlah;
        $barang->save();

        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus dan stok barang diperbarui.');
    }

    public function getHargaJual(Request $request)
    {
        // Ambil barang_id dan jumlah dari request
        $barangId = $request->input('barang_id');
        $jumlah = $request->input('jumlah');

        // Temukan barang berdasarkan barang_id
        $barang = Barang::find($barangId);
        if ($barang) {
            // Hitung total harga
            $totalHarga = $barang->harga_jual * $jumlah;

            // Format ke rupiah
            $totalHargaFormatted = formatRupiah($totalHarga);

            // Kembalikan hasil sebagai JSON
            return response()->json(['total_harga' => $totalHargaFormatted]);
        }

        return response()->json(['harga_jual' => formatRupiah(0)], 404); // Jika barang tidak ditemukan
    }

}
