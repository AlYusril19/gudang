<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\PenjualanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data penjualan dengan relasi ke customer dan barang
        $penjualan = Penjualan::with('customer', 'penjualanBarang.barang')->get();

        // Kirim data penjualan ke view
        return view('penjualan.index_penjualan', compact('penjualan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::all();
        $customers = Customer::all();
        return view('penjualan.create_penjualan', compact('barang', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barang,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Simpan data penjualan utama
            $penjualan = Penjualan::create([
                'customer_id' => $request->customer_id,
                'tanggal_penjualan' => now(),
            ]);

            // Simpan detail penjualan (barang yang dijual)
            foreach ($request->barang_ids as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);
                // Periksa apakah stok mencukupi
                if ($barang->stok < $request->jumlah[$index]) {
                    // return redirect()->back()->with('error', "Stok barang {$barang->nama_barang} tidak mencukupi");
                    return redirect()->back()->with('error', "Stok barang {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, jumlah yang diminta: {$request->jumlah[$index]}");
                }
                
                $penjualan->PenjualanBarang()->create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barangId,
                    'jumlah' => $request->jumlah[$index],
                    'harga_jual' => $barang->harga_jual,
                ]);

                // Update stok barang
                $barang->stok -= $request->jumlah[$index];
                $barang->save();
            }

            DB::commit();
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        try {
            DB::beginTransaction();

            // Temukan data penjualan
            $penjualan = Penjualan::findOrFail($id);

            // Kembalikan stok barang
            foreach ($penjualan->penjualanBarang as $detail) {
                $barang = Barang::find($detail->barang_id);
                $barang->stok += $detail->jumlah;
                $barang->save();
            }

            // Hapus detail penjualan
            $penjualan->penjualanBarang()->delete();

            // Hapus data penjualan utama
            $penjualan->delete();

            DB::commit();

            return redirect()->route('penjualan.index')->with('success', 'Data penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getStok($id)
    {
        // Temukan barang berdasarkan ID
        $barang = Barang::find($id);

        // Jika barang ditemukan, kembalikan data stok dalam format JSON
        if ($barang) {
            return response()->json([
                'stok' => $barang->stok
            ]);
        } else {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }
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
