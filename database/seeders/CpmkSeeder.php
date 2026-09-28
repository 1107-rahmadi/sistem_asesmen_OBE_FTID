<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CpmkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Helper function untuk ambil ID CPL
        $getCplId = function($kodeCpl) {
            return DB::table('cpl')->where('kode_cpl', $kodeCpl)->first()->id;
        };

        $data = [
            // CPL-01
            ['cpl_id' => $getCplId('CPL-01'), 'kode_cpmk' => 'CPMK.01.1', 'deskripsi' => 'Mampu menjelaskan konsep perancangan sistem dalam pengembangan sistem', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-01'), 'kode_cpmk' => 'CPMK.01.2', 'deskripsi' => 'Mampu menjelaskan rekayasa dalam pengembangan sistem informasi sesuai dengan kebutuhan organisasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-01'), 'kode_cpmk' => 'CPMK.01.3', 'deskripsi' => 'Mampu menguraikan prinsip penggunaan data yang legal sesuai dengan kebutuhan organisasi serta keamanannya', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-01'), 'kode_cpmk' => 'CPMK.01.4', 'deskripsi' => 'Mampu menjabarkan konsep perancangan UI/UX sesuai dengan kebutuhan organisasi', 'ambang_minimum' => 70.00],

            // CPL-02
            ['cpl_id' => $getCplId('CPL-02'), 'kode_cpmk' => 'CPMK.02.1', 'deskripsi' => 'Mampu mengidentifikasi dan menjelaskan konsep dasar sistem informasi dan infrastruktur TI dalam pengembangan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-02'), 'kode_cpmk' => 'CPMK.02.2', 'deskripsi' => 'Mampu mengolah data dengan alat dan teknik pengolahan data', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-02'), 'kode_cpmk' => 'CPMK.02.3', 'deskripsi' => 'Mampu menjelaskan konsep dasar basis data dan struktur data dalam pengembangan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-02'), 'kode_cpmk' => 'CPMK.02.4', 'deskripsi' => 'Mampu menjelaskan konsep dasar logika dalam pengembangan sistem informasi', 'ambang_minimum' => 70.00],

            // CPL-03
            ['cpl_id' => $getCplId('CPL-03'), 'kode_cpmk' => 'CPMK.03.1', 'deskripsi' => 'Mampu menentukan alat kebutuhan dan menerapkan konsep infrastruktur TI dalam pengembangan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-03'), 'kode_cpmk' => 'CPMK.03.2', 'deskripsi' => 'Mampu menganalisa data dengan alat dan teknik pengolahan data', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-03'), 'kode_cpmk' => 'CPMK.03.3', 'deskripsi' => 'Mampu menerapkan konsep struktur data dan basis data dalam proses pengembangan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-03'), 'kode_cpmk' => 'CPMK.03.4', 'deskripsi' => 'Mampu menerapkan dasar logika dalam pengembangan sistem informasi', 'ambang_minimum' => 70.00],

            // CPL-04
            ['cpl_id' => $getCplId('CPL-04'), 'kode_cpmk' => 'CPMK.04.1', 'deskripsi' => 'Mampu mengidentifikasi konsep, teknik dan metodologi pembangunan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-04'), 'kode_cpmk' => 'CPMK.04.2', 'deskripsi' => 'Mampu mengidentifikasi metodologi manajemen proyek yang sesuai dengan kebutuhan organisasi dan menerapkannya dalam aplikasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-04'), 'kode_cpmk' => 'CPMK.04.3', 'deskripsi' => 'Mampu mengkaji sistem aplikasi sesuai dengan kebutuhan organisasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-04'), 'kode_cpmk' => 'CPMK.04.4', 'deskripsi' => 'Mampu menggunakan data yang legal sesuai dengan aturan organisasi', 'ambang_minimum' => 70.00],

            // CPL-05
            ['cpl_id' => $getCplId('CPL-05'), 'kode_cpmk' => 'CPMK.05.1', 'deskripsi' => 'Mampu memahami konsep, teknik dan metodologi pembangunan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-05'), 'kode_cpmk' => 'CPMK.05.2', 'deskripsi' => 'Mampu menggunakan metode pengembangan sistem informasi sesuai dengan karakteristik kebutuhan organisasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-05'), 'kode_cpmk' => 'CPMK.05.3', 'deskripsi' => 'Mampu menentukan dan mengoperasikan sistem informasi menggunakan tools, teknik, dan proses manajemen proyek sesuai dengan metode yang tepat', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-05'), 'kode_cpmk' => 'CPMK.05.4', 'deskripsi' => 'Mampu menerapkan teknik UI/UX dalam membangun sistem aplikasi sesuai dengan karakteristik kebutuhan organisasi', 'ambang_minimum' => 70.00],

            // CPL-06
            ['cpl_id' => $getCplId('CPL-06'), 'kode_cpmk' => 'CPMK.06.1', 'deskripsi' => 'Mampu menerapkan konsep, teknik dan metodologi manajemen proyek sistem informasi dalam aplikasi proyek sistem', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-06'), 'kode_cpmk' => 'CPMK.06.2', 'deskripsi' => 'Mampu menerapkan sistem komputer secara prosedural untuk menyelesaikan penelitian yang menghasilkan solusi bagi organisasi', 'ambang_minimum' => 70.00],

            // CPL-07
            ['cpl_id' => $getCplId('CPL-07'), 'kode_cpmk' => 'CPMK.07.1', 'deskripsi' => 'Mampu memahami dan menerapkan konsep/teori dasar perangkat lunak sistem operasi dalam pengoperasian sistem komputer', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-07'), 'kode_cpmk' => 'CPMK.07.2', 'deskripsi' => 'Mampu memahami dan menerapkan konsep dasar jaringan komputer sederhana dalam pengoperasian sistem komputer', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-07'), 'kode_cpmk' => 'CPMK.07.3', 'deskripsi' => 'Menerapkan Konsep Jaringan Komputer sederhana', 'ambang_minimum' => 70.00],

            // CPL-08
            ['cpl_id' => $getCplId('CPL-08'), 'kode_cpmk' => 'CPMK.08.1', 'deskripsi' => 'Mampu memahami konsep dasar bahasa pemrograman untuk membangun sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-08'), 'kode_cpmk' => 'CPMK.08.2', 'deskripsi' => 'Mampu menganalisa kebutuhan pengguna dan menerapkan dalam bahasa pemrograman untuk membangun sistem informasi', 'ambang_minimum' => 70.00],

            // CPL-09
            ['cpl_id' => $getCplId('CPL-09'), 'kode_cpmk' => 'CPMK.09.1', 'deskripsi' => 'Mampu mengidentifikasi kebutuhan, mengelola dan berinovasi dalam membangun bisnis dengan memanfaatkan teknologi informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-09'), 'kode_cpmk' => 'CPMK.09.2', 'deskripsi' => 'Mampu memahami kebutuhan dalam pengembangan organisasi', 'ambang_minimum' => 70.00],

            // CPL-10
            ['cpl_id' => $getCplId('CPL-10'), 'kode_cpmk' => 'CPMK.10.1', 'deskripsi' => 'Mampu meyusun kalimat dengan baik dalam karya ilmiah yang berhubungan dengan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-10'), 'kode_cpmk' => 'CPMK.10.2', 'deskripsi' => 'Mampu menggunakan tata cara / kosakata berbahasa dan menyusun kalimat yang baik dan benar yang berhubungan dengan sistem informasi', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-10'), 'kode_cpmk' => 'CPMK.10.3', 'deskripsi' => 'Mampu menulis, membaca dan mendengarkan dengan baik bahasa Inggris yang terstandarisasi', 'ambang_minimum' => 70.00],

            // CPL-11
            ['cpl_id' => $getCplId('CPL-11'), 'kode_cpmk' => 'CPMK.11.1', 'deskripsi' => 'Mampu memahami dan menerapkan ajaran dan nilai-nilai islam yang berkaitan dengan ibadah, akhlah dan aqidah', 'ambang_minimum' => 70.00],
            ['cpl_id' => $getCplId('CPL-11'), 'kode_cpmk' => 'CPMK.11.2', 'deskripsi' => 'Mampu memahami dan menerapkan pedoman nilai-nilai dasar Pancasila untuk menkaji, menganalisis, dan memecahkan masalah-masalah pembangunan bangsa dan negara', 'ambang_minimum' => 70.00],
        ];

        DB::table('cpmk')->insert($data);
    }
}
