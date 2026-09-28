<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'kelas_raw',
    ];

    /**
     * Relasi ke Nilai Mahasiswa
     */
    public function nilaiMahasiswa(): HasMany
    {
        return $this->hasMany(NilaiMahasiswa::class, 'nim', 'nim');
    }

    /**
     * Relasi ke Capaian CPL Mahasiswa
     */
    public function capaianCplMahasiswa(): HasMany
    {
        return $this->hasMany(CapaianCplMahasiswa::class, 'nim', 'nim');
    }
}
