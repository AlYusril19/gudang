<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminBerandaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // get tanggal sekarang
        $now = Carbon::now();

        // Jika ada filter bulan dan tahun dari request, gunakan itu
        $bulanDipilih = $request->input('bulan', $now->month); // Default bulan sekarang
        $tahunDipilih = $request->input('tahun', $now->year); // Default tahun sekarang

        // Bulan sebelumnya untuk perbandingan
        $bulanSebelumnya = Carbon::createFromDate($tahunDipilih, $bulanDipilih)->subMonth()->month;
        $tahunSebelumnya = Carbon::createFromDate($tahunDipilih, $bulanDipilih)->subMonth()->year;

        // Stok menipis
        $stokMinim = Barang::where('status', 'aktif')
            ->whereRaw('stok <= stok_minimal')
            ->where('nama_barang', 'not like', '%second%')
            ->count();

        // Get pembelian sekarang
        $pembelianSekarang = Pembelian::whereMonth('tanggal_pembelian', $bulanDipilih)
            ->whereYear('tanggal_pembelian', $tahunDipilih)
            ->whereNull('kegiatan')
            ->sum('total_harga');

        // Get pembelian sebelumnya
        $pembelianSebelumnya = Pembelian::whereMonth('tanggal_pembelian', $bulanSebelumnya)
            ->whereYear('tanggal_pembelian', $tahunSebelumnya)
            ->whereNull('kegiatan')
            ->sum('total_harga');

        // Banding pembelian sekarang dan sebelumnya
        $bandingPembelian = 0;
        if ($pembelianSebelumnya) {
            $bandingPembelian = round(($pembelianSekarang - $pembelianSebelumnya) / $pembelianSebelumnya * 100, 2);
        }

        // Get penjualan sekarang
        $penjualanSekarang = Penjualan::whereMonth('tanggal_penjualan', $bulanDipilih)
            ->whereYear('tanggal_penjualan', $tahunDipilih)
            ->where('customer_id', '!=', null)
            ->sum('total_harga');

        // Get penjualan sebelumnya
        $penjualanSebelumnya = Penjualan::whereMonth('tanggal_penjualan', $bulanSebelumnya)
            ->whereYear('tanggal_penjualan', $tahunSebelumnya)
            ->where('customer_id', '!=', null)
            ->sum('total_harga');

        // Get perbaikan sekarang
        $perbaikanSekarang = Penjualan::whereMonth('tanggal_penjualan', $bulanDipilih)
            ->whereYear('tanggal_penjualan', $tahunDipilih)
            ->where('kegiatan', 'perbaikan')
            ->sum('total_harga');

        // Get perbaikan sebelumnya
        $perbaikanSebelumnya = Penjualan::whereMonth('tanggal_penjualan', $bulanSebelumnya)
            ->whereYear('tanggal_penjualan', $tahunSebelumnya)
            ->where('kegiatan', 'perbaikan')
            ->sum('total_harga');

        // Get asset barang
        $barangs = Barang::where('status', 'aktif')->get();

        // Menghitung total harga_beli * stok
        $totalAsset = $barangs->sum(function ($barang) {
            return $barang->harga_beli * $barang->stok;
        });

        // Banding penjualan sekarang dan sebelumnya
        $bandingPenjualan = 0;
        if ($penjualanSebelumnya) {
            $bandingPenjualan = round((($penjualanSekarang - $penjualanSebelumnya) / $penjualanSebelumnya) * 100, 2);
        }

        // Banding perbaikan sekarang dan sebelumnya
        $bandingPerbaikan = 0;
        if ($perbaikanSebelumnya) {
            $bandingPerbaikan = round(($perbaikanSekarang - $perbaikanSebelumnya) / $perbaikanSebelumnya * 100, 2);
        }

        return view('admin.beranda', [
            'pembelianSekarang' => $pembelianSekarang,
            'bandingPembelian' => $bandingPembelian,
            'penjualanSekarang' => $penjualanSekarang,
            'bandingPenjualan' => $bandingPenjualan,
            'bandingPerbaikan' => $bandingPerbaikan,
            'perbaikanSekarang' => $perbaikanSekarang,
            'stokMinim' => $stokMinim,
            'totalAsset' => $totalAsset,
            'bulanDipilih' => $bulanDipilih,
            'tahunDipilih' => $tahunDipilih
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }
}
