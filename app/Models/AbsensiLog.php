<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiLog extends Model
{
    use HasFactory;
    protected $table = 'absensi_logs';

    protected $fillable = [
        'absensi_id',
        'action',
        'lat',
        'lng',
        'foto',
        'ip_address',
        'user_agent',
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
}
