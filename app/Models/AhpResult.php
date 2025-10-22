<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'skor_total',
        'peringkat',
        'keterangan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
