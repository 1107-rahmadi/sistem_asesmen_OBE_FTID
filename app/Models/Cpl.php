<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpl';

    protected $fillable = [
        'kode_cpl',
        'deskripsi',
        'ambang_minimum',
        'prodi_id',
    ];

    protected $casts = [
        'ambang_minimum' => 'decimal:2',
    ];

    /**
     * Relasi ke Prodi
     */
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Relasi ke CPMK
     */
    public function cpmk(): HasMany
    {
        return $this->hasMany(Cpmk::class, 'cpl_id');
    }

    /**
     * Relasi ke Capaian CPL Mahasiswa
     */
    public function capaianMahasiswa(): HasMany
    {
        return $this->hasMany(CapaianCplMahasiswa::class, 'cpl_id');
    }
}
