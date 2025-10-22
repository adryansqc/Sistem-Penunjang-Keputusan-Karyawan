<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopsisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'skor_topsis',
        'peringkat',
        'keterangan',
    ];

    protected $casts = [
        'skor_topsis' => 'float',
        'peringkat' => 'integer',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
