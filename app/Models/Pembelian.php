<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $fillable = [
        'supplier_id', 
        'tanggal_pembelian', 
        'total_harga',
        'user_id',
        'kegiatan'
    ];

    public function pembelianBarang()
    {
        return $this->hasMany(PembelianBarang::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
