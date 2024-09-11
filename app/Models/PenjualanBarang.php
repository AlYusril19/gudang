<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanBarang extends Model
{
    use HasFactory;

    protected $table = 'penjualan_barang'; // Nama tabel yang digunakan
    protected $fillable = ['penjualan_id', 'barang_id', 'jumlah', 'harga_jual'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
