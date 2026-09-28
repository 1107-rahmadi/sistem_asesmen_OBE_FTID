<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmk';

    protected $fillable = [
        'cpl_id',
        'kode_cpmk',
        'deskripsi',
        'ambang_minimum',
    ];

    protected $casts = [
        'ambang_minimum' => 'decimal:2',
    ];

    /**
     * Relasi ke CPL (induk)
     */
    public function cpl(): BelongsTo
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }

    /**
     * Relasi ke Matriks CPMK
     */
    public function matriksCpmk(): HasMany
    {
        return $this->hasMany(MatriksCpmk::class, 'cpmk_id');
    }
}
