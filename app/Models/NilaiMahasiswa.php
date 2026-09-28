<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'nim',
        'nama_mk_siakad',
        'tugas',
        'uts',
        'uas',
        'partisipatif',
        'nilai_akhir',
        'nilai_skala4',
        'huruf',
    ];

    protected $casts = [
        'tugas' => 'decimal:2',
        'uts' => 'decimal:2',
        'uas' => 'decimal:2',
        'partisipatif' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'nilai_skala4' => 'decimal:2',
    ];

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    /**
     * Relasi ke Pemetaan MK (berdasarkan nama_mk_siakad)
     */
    public function pemetaanMk()
    {
        return $this->belongsTo(PemetaanMk::class, 'nama_mk_siakad', 'nama_mk_siakad');
    }
}
