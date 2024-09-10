<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    // use HasFactory;
    protected $table = 'penjualan';

    protected $fillable = [
        'barang_id', 
        'customer_id', 
        'jumlah', 
        'harga_jual', 
        'tanggal_penjualan'
    ];
    protected $casts = [
        'tanggal_penjualan' => 'datetime',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
