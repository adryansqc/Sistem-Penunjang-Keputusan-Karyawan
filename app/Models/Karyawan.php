<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'posisi',
        'masa_kontrak_bulan',
        'tanggal_masuk',
    ];

    public function evaluasis()
    {
        return $this->hasMany(Evaluasi::class);
    }

    public function ahpResult()
    {
        return $this->hasOne(AhpResult::class);
    }
}
