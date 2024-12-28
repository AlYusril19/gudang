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
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query untuk mengambil data penjualan
        $query = Penjualan::with('customer', 'user' , 'penjualanBarang.barang')
                    ->orderBy('tanggal_penjualan', 'DESC')
                    ->whereNotNull('customer_id');

        // Jika ada input pencarian
        if ($search) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhere('tanggal_penjualan', 'like', "%$search%");
        }

        // Dapatkan hasil query
        $penjualan = $query->get();

        // Kirim hasil ke view
        return view('penjualan.index_penjualan', compact('penjualan'));
    }

    public function indexBarangKeluar(Request $request)
    {
        $search = $request->input('search');

        // Query untuk mengambil data penjualan
        $query = Penjualan::with('customer', 'user' , 'penjualanBarang.barang')
                    ->orderBy('tanggal_penjualan', 'DESC')
                    ->where('kegiatan', '!=', 'null')
                    ->WhereNull('customer_id');

        // Jika ada input pencarian
        if ($search) {
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%");
            })
            ->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
            ->orWhere('tanggal_penjualan', 'like', "%$search%")
            ->orWhere('kegiatan', 'like', "%$search%");
        }

        // Dapatkan hasil query
        $penjualan = $query->get();

        // Kirim hasil ke view
        return view('penjualan.index_barang_keluar', compact('penjualan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::where('status', 'aktif')->orderBy('nama_barang', 'asc')->get();
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

            $userId = auth()->user()->id;
            // dd($userId);
            // Simpan data penjualan utama
            $penjualan = Penjualan::create([
                'user_id' => $userId,
                'customer_id' => $request->customer_id,
                'tanggal_penjualan' => now(),
            ]);
            $totalHarga = 0;

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
                $totalHarga += $barang->harga_jual * $request->jumlah[$index];
            }
            $penjualan->total_harga = $totalHarga;
            $penjualan->save();

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
            $user = auth()->user();
            if ($user->role != 'superadmin') {
                if ($penjualan->user_id != $user->id) {
                    return redirect()->back()->with('error', 'Data gagal dihapus karena berkaitan dengan user lain, silahkan hubungi user yang bersangkutan');
                }
            }

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

            return redirect()->back()->with('success', 'Data berhasil dihapus: ');
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

    /*  */
    /* SEND API */
    /*  */
    public function storeApi(Request $request)
    {
        // Validasi data yang dikirim dari web baru
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'barang' => 'required|array',
            'barang.*.id' => 'required|exists:barang,id',
            'barang.*.jumlah' => 'required|integer|min:1',
            'kegiatan' => 'required',
            'tanggal_penjualan' => 'required',
            'customer_id' => 'nullable',
            'laporan_id' => 'required',
        ]);

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Simpan data penjualan utama
            $penjualan = Penjualan::create([
                'user_id' => $request->user_id,
                'kegiatan' => $request->kegiatan,
                'customer_id' => $request->customer_id, // Jika tidak ada customer, diisi null
                'tanggal_penjualan' => $request->tanggal_penjualan,
                'laporan_id' => $request->laporan_id,
            ]);

            $totalHarga = 0;

            // Looping untuk setiap barang yang dikirim dari web baru
            foreach ($request->barang as $barangData) {
                $barang = Barang::findOrFail($barangData['id']);

                // Cek stok barang apakah mencukupi
                if ($barang->stok < $barangData['jumlah']) {
                    // Jika stok tidak cukup, rollback transaksi
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok barang {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}, jumlah diminta: {$barangData['jumlah']}"
                    ], 400);
                }

                if ($request->kegiatan === 'mitra') {
                    $hargaJual = $barang->harga_jual;
                } else {
                    $hargaJual = $barang->harga_beli;
                }
                // Simpan detail penjualan barang
                $penjualan->PenjualanBarang()->create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barangData['id'],
                    'jumlah' => $barangData['jumlah'],
                    'harga_jual' => $hargaJual,
                ]);

                // Update stok barang
                $barang->stok -= $barangData['jumlah'];
                $barang->save();

                // Hitung total harga penjualan
                $totalHarga += $barang->harga_jual * $barangData['jumlah'];
            }

            // Simpan total harga di penjualan
            $penjualan->total_harga = $totalHarga;
            $penjualan->save();

            // Commit transaksi
            DB::commit();

            // Berikan respon sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Penjualan berhasil disimpan.',
                'data' => $penjualan
            ], 201);

        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollBack();

            // Berikan respon gagal
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyApi(Request $request)
    {
        try {
            DB::beginTransaction();

            // Ambil data created_at dan user_id dari request
            $laporanId = $request->laporan_id;

            // Temukan data penjualan berdasarkan created_at dan user_id
            $penjualan = Penjualan::where('laporan_id', $laporanId)
                                ->first();

            // Jika penjualan tidak ditemukan, kembalikan response error
            if (!$penjualan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penjualan tidak ditemukan atau bukan milik user yang bersangkutan.'
                ], 404); // 404 Not Found
            }

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

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data penjualan berhasil dihapus.'
            ], 200); // 200 OK
        } catch (\Exception $e) {
            DB::rollBack();
            // Response error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    public function getPenjualanApi(string $id) {
        // Temukan data penjualan berdasarkan created_at dan user_id
        $penjualan = Penjualan::with('penjualanBarang.barang')
                            ->where('laporan_id', $id)
                            ->first();
        return response()->json($penjualan);
    }

}
