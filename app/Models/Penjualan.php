<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = ['customer_id', 'tanggal_penjualan', 'total_harga'];

    public function penjualanBarang()
    {
        return $this->hasMany(PenjualanBarang::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
