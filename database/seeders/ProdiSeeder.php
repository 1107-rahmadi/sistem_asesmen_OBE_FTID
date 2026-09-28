<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $prodis = [
            ['kode_prodi' => 'MI', 'nama_prodi' => 'Manajemen Informatika'],
            ['kode_prodi' => 'TI', 'nama_prodi' => 'Teknik Informatika'],
            ['kode_prodi' => 'KA', 'nama_prodi' => 'Komputerisasi Akuntansi'],
            ['kode_prodi' => 'SI', 'nama_prodi' => 'Sistem Informasi'],
            ['kode_prodi' => 'TK', 'nama_prodi' => 'Teknik Komputer'],
        ];

        foreach ($prodis as $prodi) {
            DB::table('prodi')->updateOrInsert(
                ['kode_prodi' => $prodi['kode_prodi']],
                ['nama_prodi' => $prodi['nama_prodi']]
            );
        }
    }
}
