<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapaianCplMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'capaian_cpl_mahasiswa';

    protected $fillable = [
        'nim',
        'cpl_id',
        'persen_capaian',
        'jumlah_mk_dipakai',
        'status_tercapai',
        'dihitung_pada',
    ];

    protected $casts = [
        'persen_capaian' => 'decimal:2',
        'jumlah_mk_dipakai' => 'integer',
        'status_tercapai' => 'boolean',
        'dihitung_pada' => 'datetime',
    ];

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    /**
     * Relasi ke CPL
     */
    public function cpl(): BelongsTo
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }
}
