<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminBerandaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();
        $bulanSekarang = $now->month;
        $tahunSekarang = $now->year;
        $pembelianSekarang = Pembelian::whereMonth('tanggal_pembelian', $bulanSekarang)
                                ->whereYear('tanggal_pembelian', $tahunSekarang)
                                ->sum('total_harga');
        $bulanKemarin = $now->subMonth()->month;
        $tahunKemarin = $now->subMonth()->year;
        $pembelianKemarin = Pembelian::whereMonth('tanggal_pembelian', $bulanKemarin)
                                ->whereYear('tanggal_pembelian', $tahunKemarin)
                                ->sum('total_harga');
        $bandingPembelian = 0;
        if ($pembelianKemarin) {
            $bandingPembelian = round(($pembelianSekarang-$pembelianKemarin)/$pembelianKemarin*100, 2);
        }
        return view('admin.beranda', [
            'pembelianSekarang' => $pembelianSekarang,
            'bandingPembelian' => $bandingPembelian
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
