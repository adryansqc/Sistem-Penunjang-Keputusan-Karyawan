<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kriteriakomparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'kriteria1_id',
        'kriteria2_id',
        'nilai',
    ];

    /**
     * Get the first kriteria involved in the comparison.
     */
    public function kriteria1(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria1_id');
    }

    /**
     * Get the second kriteria involved in the comparison.
     */
    public function kriteria2(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria2_id');
    }
}
