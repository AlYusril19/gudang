<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lat_masuk',
        'lng_masuk',
        'lat_pulang',
        'lng_pulang',
        'foto_masuk',
        'foto_pulang',
        'status',
        'keterangan',
        'device_id',
        'platform',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_masuk'  => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    /**
     * Relasi ke User
     * Absensi milik satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke absensi logs (audit)
     */
    public function logs()
    {
        return $this->hasMany(AbsensiLog::class);
    }
}
