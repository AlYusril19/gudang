<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Barang::create([
            'kode_barang' => 'K-001',
            'nama_barang' => 'Router Tenda N301',
            'deskripsi' => 'Router 2.4 Ghz',
            'harga_beli' => 135000,
            'harga_jual' => 143000,
            'stok' => 5,
        ]);

        Barang::create([
            'kode_barang' => 'K-002',
            'nama_barang' => 'Router Totolink N200RE v5',
            'deskripsi' => 'Router 2.4 Ghz',
            'harga_beli' => 145000,
            'harga_jual' => 155000,
            'stok' => 3,
        ]);
    }
}
