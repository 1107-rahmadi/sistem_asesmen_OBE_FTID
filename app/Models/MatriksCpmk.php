<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatriksCpmk extends Model
{
    use HasFactory;

    protected $table = 'matriks_cpmk';

    protected $fillable = [
        'cpmk_id',
        'kode_mk',
        'nama_mk_matrix',
        'bobot_partisipatif',
        'bobot_tugas',
        'bobot_uts',
        'bobot_uas',
        'skor_maksimal',
    ];

    protected $casts = [
        'bobot_partisipatif' => 'decimal:2',
        'bobot_tugas' => 'decimal:2',
        'bobot_uts' => 'decimal:2',
        'bobot_uas' => 'decimal:2',
        'skor_maksimal' => 'decimal:2',
    ];

    /**
     * Relasi ke CPMK
     */
    public function cpmk(): BelongsTo
    {
        return $this->belongsTo(Cpmk::class, 'cpmk_id');
    }
}
