<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'harga_beli',
        'harga_jual',
        'stok',
    ];
    public function penjualans()
    {
        return $this->belongsToMany(Penjualan::class, 'penjualan_barang')
                    ->withPivot('jumlah', 'harga_jual')
                    ->withTimestamps();
    }
}
