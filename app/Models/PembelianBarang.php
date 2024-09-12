<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    use HasFactory;
    protected $table = 'pembelian_barang'; // Nama tabel yang digunakan
    protected $fillable = ['pembelian_id', 'barang_id', 'jumlah', 'harga_beli'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
