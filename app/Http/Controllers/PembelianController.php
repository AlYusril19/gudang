<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query untuk mengambil data pembelian
        $query = Pembelian::with('supplier', 'pembelianBarang.barang')->orderBy('created_at', 'DESC');;

        // Jika ada input pencarian
        if ($search) {
            $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })
            ->orWhere('tanggal_pembelian', 'like', "%$search%");
        }

        // Dapatkan hasil query
        $pembelians = $query->get();

        // Kirim hasil ke view
        return view('pembelian.index_pembelian', compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $barang = Barang::where('status', 'aktif')->orderBy('nama_barang', 'asc')->get();
        return view('pembelian.create_pembelian', compact('barang', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barang,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga_beli' => 'required|array',
            'harga_beli.*' => 'integer',
        ]);

        try {
            DB::beginTransaction();

            // Simpan data pembelian utama
            $pembelian = Pembelian::create([
                'supplier_id' => $request->supplier_id,
                'tanggal_pembelian' => now(),
            ]);
            $totalHarga = 0;

            // Simpan detail pembelian (barang yang dijual)
            foreach ($request->barang_ids as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);
                
                $pembelian->pembelianBarang()->create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barangId,
                    'jumlah' => $request->jumlah[$index],
                    'harga_beli' => $request->harga_beli[$index],
                ]);

                // Update stok barang
                $barang->stok += $request->jumlah[$index];
                $barang->harga_beli = $request->harga_beli[$index];
                $barang->save();
                // Hitung total harga
                $totalHarga += $request->harga_beli[$index]*$request->jumlah[$index];
            }
            $pembelian->total_harga = $totalHarga;
            $pembelian->save();

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
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

            // Temukan data pembelian
            $pembelian = Pembelian::findOrFail($id);

            // Kembalikan stok barang
            foreach ($pembelian->pembelianBarang as $detail) {
                $barang = Barang::find($detail->barang_id);
                $barang->stok -= $detail->jumlah;
                $barang->save();
            }

            // Hapus detail pembelian
            $pembelian->pembelianBarang()->delete();

            // Hapus data pembelian utama
            $pembelian->delete();

            DB::commit();

            return redirect()->route('pembelian.index')->with('success', 'Data Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getHargaBeli(Request $request)
    {
        $barang = Barang::find($request->id);
        if ($barang) {
            return response()->json([
                'harga_beli' => $barang->harga_beli
            ]);
        }
        return response()->json(['error' => 'Barang tidak ditemukan'], 404);
    }

}
