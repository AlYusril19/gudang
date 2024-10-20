<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminBerandaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get tanggal sekarang dan kemarin
        $now = Carbon::now();
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;
        $bulanKemarin = $now->subMonth()->month;
        $tahunKemarin = $now->subMonth()->year;

        // get pembelian sekarang
        $pembelianSekarang = Pembelian::whereMonth('tanggal_pembelian', $bulanSekarang)
                                ->whereYear('tanggal_pembelian', $tahunSekarang)
                                ->sum('total_harga');
        // get pembelian kemarin
        $pembelianKemarin = Pembelian::whereMonth('tanggal_pembelian', $bulanKemarin)
                                ->whereYear('tanggal_pembelian', $tahunKemarin)
                                ->sum('total_harga');
        // banding pembelian sekarang dan kemarin
        $bandingPembelian = 0;
        if ($pembelianKemarin) {
            $bandingPembelian = round(($pembelianSekarang-$pembelianKemarin)/$pembelianKemarin*100, 2);
        }

        // get penjualan sekarang
        $penjualanSekarang = Penjualan::whereMonth('tanggal_penjualan', $bulanSekarang)
                                ->whereYear('tanggal_penjualan', $tahunSekarang)
                                ->where('customer_id', '!=', null)
                                ->sum('total_harga');
        // get pembelian kemarin
        $penjualanKemarin = Penjualan::whereMonth('tanggal_penjualan', $bulanKemarin)
                                ->whereYear('tanggal_penjualan', $tahunKemarin)
                                ->where('customer_id', '!=', null)
                                ->sum('total_harga');

        $perbaikanSekarang = Penjualan::whereMonth('tanggal_penjualan', $bulanSekarang)
                                ->whereYear('tanggal_penjualan', $tahunSekarang)
                                ->where('kegiatan', 'perbaikan')
                                ->sum('total_harga');
        // get pembelian kemarin
        $perbaikanKemarin = Penjualan::whereMonth('tanggal_penjualan', $bulanKemarin)
                                ->whereYear('tanggal_penjualan', $tahunKemarin)
                                ->where('kegiatan', 'perbaikan')
                                ->sum('total_harga');
        // banding pembelian sekarang dan kemarin
        $bandingPenjualan = 0;
        if ($pembelianKemarin) {
            $bandingPenjualan = round(($penjualanSekarang-$penjualanKemarin)/$penjualanKemarin*100, 2);
        }
        $bandingPerbaikan = 0;
        if ($perbaikanKemarin) {
            $bandingPerbaikan = round(($perbaikanSekarang-$perbaikanKemarin)/$perbaikanKemarin*100, 2);
        }
        return view('admin.beranda', [
            'pembelianSekarang' => $pembelianSekarang,
            'bandingPembelian' => $bandingPembelian,
            'penjualanSekarang' => $penjualanSekarang,
            'bandingPenjualan' => $bandingPenjualan,
            'bandingPerbaikan' => $bandingPerbaikan,
            'perbaikanSekarang' => $perbaikanSekarang
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
