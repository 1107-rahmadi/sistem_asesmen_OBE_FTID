<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemetaanMk extends Model
{
    use HasFactory;

    protected $table = 'pemetaan_mk';

    protected $fillable = [
        'nama_mk_siakad',
        'kode_mk',
        'status_terverifikasi',
        'catatan',
    ];

    protected $casts = [
        'status_terverifikasi' => 'boolean',
    ];
}
