<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatriksCpmkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Helper function untuk ambil ID CPMK
        $getCpmkId = function($kodeCpmk) {
            return DB::table('cpmk')->where('kode_cpmk', $kodeCpmk)->first()->id;
        };

        $data = [
            ['cpmk_id' => $getCpmkId('CPMK.01.1'), 'kode_mk' => 'MK-16', 'nama_mk_matrix' => 'Analisis dan Perancangan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.01.2'), 'kode_mk' => 'MK-16', 'nama_mk_matrix' => 'Analisis dan Perancangan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.01.3'), 'kode_mk' => 'MK-37', 'nama_mk_matrix' => 'Etika Profesi Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 10, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.01.2'), 'kode_mk' => 'MK-44', 'nama_mk_matrix' => 'Rekayasa Perangkat Lunak', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.01.3'), 'kode_mk' => 'MK-47', 'nama_mk_matrix' => 'Keamanan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 10, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.01.4'), 'kode_mk' => 'MK-48', 'nama_mk_matrix' => 'Perancangan UI/UX', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.02.1'), 'kode_mk' => 'MK-01', 'nama_mk_matrix' => 'Pengantar Teknologi Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.02.1'), 'kode_mk' => 'MK-03', 'nama_mk_matrix' => 'Sistem Informasi Manajemen', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.02.2'), 'kode_mk' => 'MK-06', 'nama_mk_matrix' => 'Statistika', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.2'), 'kode_mk' => 'MK-07', 'nama_mk_matrix' => 'Struktur Diskrit', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.3'), 'kode_mk' => 'MK-08', 'nama_mk_matrix' => 'Aplikasi Basis Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.3'), 'kode_mk' => 'MK-12', 'nama_mk_matrix' => 'Aplikasi Basis Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.3'), 'kode_mk' => 'MK-14', 'nama_mk_matrix' => 'Struktur Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.4'), 'kode_mk' => 'MK-20', 'nama_mk_matrix' => 'Algoritma dan Pemrograman', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.3'), 'kode_mk' => 'MK-22', 'nama_mk_matrix' => 'Pemrograman Berorientasi Objek 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.02.3'), 'kode_mk' => 'MK-24', 'nama_mk_matrix' => 'Pemrograman Berorientasi Objek 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.03.1'), 'kode_mk' => 'MK-02', 'nama_mk_matrix' => 'Prak. Pengantar Teknologi Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.03.2'), 'kode_mk' => 'MK-06', 'nama_mk_matrix' => 'Statistika', 'bobot_partisipatif' => 1, 'bobot_tugas' => 1, 'bobot_uts' => 1, 'bobot_uas' => 2, 'skor_maksimal' => 5],
            ['cpmk_id' => $getCpmkId('CPMK.03.2'), 'kode_mk' => 'MK-07', 'nama_mk_matrix' => 'Struktur Diskrit', 'bobot_partisipatif' => 1, 'bobot_tugas' => 1, 'bobot_uts' => 1, 'bobot_uas' => 2, 'skor_maksimal' => 5],
            ['cpmk_id' => $getCpmkId('CPMK.03.3'), 'kode_mk' => 'MK-09', 'nama_mk_matrix' => 'Prak. Aplikasi Basis Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.03.2'), 'kode_mk' => 'MK-12', 'nama_mk_matrix' => 'Prak. Aplikasi Basis Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.03.3'), 'kode_mk' => 'MK-15', 'nama_mk_matrix' => 'Prak. Struktur Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.03.4'), 'kode_mk' => 'MK-21', 'nama_mk_matrix' => 'Prak. Algoritma dan Pemrograman', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.03.3'), 'kode_mk' => 'MK-23', 'nama_mk_matrix' => 'Prak. Pemrograman Berorientasi Objek 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.03.3'), 'kode_mk' => 'MK-25', 'nama_mk_matrix' => 'Prak. Pemrograman Berorientasi Objek 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.04.1'), 'kode_mk' => 'MK-10', 'nama_mk_matrix' => 'Manajemen Proyek Sistem Informasi / Capstone Project', 'bobot_partisipatif' => 3, 'bobot_tugas' => 3, 'bobot_uts' => 4, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.04.2'), 'kode_mk' => 'MK-12', 'nama_mk_matrix' => 'Sistem Terintegrasi', 'bobot_partisipatif' => 3, 'bobot_tugas' => 3, 'bobot_uts' => 4, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.04.2'), 'kode_mk' => 'MK-16', 'nama_mk_matrix' => 'Analisis dan Perancangan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.2'), 'kode_mk' => 'MK-17', 'nama_mk_matrix' => 'Prak. Analisis dan Perancangan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.1'), 'kode_mk' => 'MK-18', 'nama_mk_matrix' => 'Kerja Praktek / Magang', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.3'), 'kode_mk' => 'MK-18', 'nama_mk_matrix' => 'Kerja Praktek / Magang', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.1'), 'kode_mk' => 'MK-19', 'nama_mk_matrix' => 'Karya Tulis Ilmiah', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.4'), 'kode_mk' => 'MK-37', 'nama_mk_matrix' => 'Etika Profesi Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.04.2'), 'kode_mk' => 'MK-44', 'nama_mk_matrix' => 'Rekayasa Perangkat Lunak', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.05.1'), 'kode_mk' => 'MK-10', 'nama_mk_matrix' => 'Manajemen Proyek Sistem Informasi / Capstone Project', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.05.2'), 'kode_mk' => 'MK-11', 'nama_mk_matrix' => 'Sistem Basis Data', 'bobot_partisipatif' => 5, 'bobot_tugas' => 5, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 30],
            ['cpmk_id' => $getCpmkId('CPMK.05.3'), 'kode_mk' => 'MK-17', 'nama_mk_matrix' => 'Prak. Analisis dan Perancangan Sistem Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.05.2'), 'kode_mk' => 'MK-44', 'nama_mk_matrix' => 'Rekayasa Perangkat Lunak', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.05.4'), 'kode_mk' => 'MK-49', 'nama_mk_matrix' => 'Prak. Perancangan UI/UX', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.06.1'), 'kode_mk' => 'MK-13', 'nama_mk_matrix' => 'Prak. Sistem Terintegrasi', 'bobot_partisipatif' => 5, 'bobot_tugas' => 10, 'bobot_uts' => 15, 'bobot_uas' => 20, 'skor_maksimal' => 50],
            ['cpmk_id' => $getCpmkId('CPMK.06.2'), 'kode_mk' => 'MK-18', 'nama_mk_matrix' => 'Kerja Praktek / Magang', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.06.2'), 'kode_mk' => 'MK-19', 'nama_mk_matrix' => 'Karya Tulis Ilmiah', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.07.2'), 'kode_mk' => 'MK-01', 'nama_mk_matrix' => 'Pengantar Teknologi Informasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.07.1'), 'kode_mk' => 'MK-04', 'nama_mk_matrix' => 'Sistem Operasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.07.1'), 'kode_mk' => 'MK-05', 'nama_mk_matrix' => 'Prak. Sistem Operasi', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.07.2'), 'kode_mk' => 'MK-45', 'nama_mk_matrix' => 'Jaringan Komputer', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.07.3'), 'kode_mk' => 'MK-46', 'nama_mk_matrix' => 'Prak. Jaringan Komputer', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.08.2'), 'kode_mk' => 'MK-10', 'nama_mk_matrix' => 'Manajemen Proyek Sistem Informasi / Capstone Project', 'bobot_partisipatif' => 10, 'bobot_tugas' => 20, 'bobot_uts' => 30, 'bobot_uas' => 40, 'skor_maksimal' => 100],
            ['cpmk_id' => $getCpmkId('CPMK.08.1'), 'kode_mk' => 'MK-11', 'nama_mk_matrix' => 'Sistem Basis Data', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.08.1'), 'kode_mk' => 'MK-26', 'nama_mk_matrix' => 'Pemrograman Mobile 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.08.2'), 'kode_mk' => 'MK-27', 'nama_mk_matrix' => 'Prak. Pemrograman Mobile 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.08.1'), 'kode_mk' => 'MK-28', 'nama_mk_matrix' => 'Pemrograman Mobile 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.08.2'), 'kode_mk' => 'MK-29', 'nama_mk_matrix' => 'Prak. Pemrograman Mobile 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.08.1'), 'kode_mk' => 'MK-30', 'nama_mk_matrix' => 'Pemrograman WEB 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 2, 'bobot_uts' => 3, 'bobot_uas' => 3, 'skor_maksimal' => 10],
            ['cpmk_id' => $getCpmkId('CPMK.08.2'), 'kode_mk' => 'MK-31', 'nama_mk_matrix' => 'Prak. Pemrograman WEB 1', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.08.1'), 'kode_mk' => 'MK-32', 'nama_mk_matrix' => 'Pemrograman WEB 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.08.2'), 'kode_mk' => 'MK-33', 'nama_mk_matrix' => 'Prak. Pemrograman WEB 2', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 5, 'bobot_uas' => 5, 'skor_maksimal' => 15],
            ['cpmk_id' => $getCpmkId('CPMK.09.1'), 'kode_mk' => 'MK-42', 'nama_mk_matrix' => 'Technopreneurship', 'bobot_partisipatif' => 5, 'bobot_tugas' => 10, 'bobot_uts' => 15, 'bobot_uas' => 20, 'skor_maksimal' => 50],
            ['cpmk_id' => $getCpmkId('CPMK.09.2'), 'kode_mk' => 'MK-43', 'nama_mk_matrix' => 'Pengantar Manajemen', 'bobot_partisipatif' => 5, 'bobot_tugas' => 10, 'bobot_uts' => 15, 'bobot_uas' => 20, 'skor_maksimal' => 50],
            ['cpmk_id' => $getCpmkId('CPMK.10.1'), 'kode_mk' => 'MK-34', 'nama_mk_matrix' => 'Bahasa Indonesia', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.10.2'), 'kode_mk' => 'MK-34', 'nama_mk_matrix' => 'Bahasa Indonesia', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.10.1'), 'kode_mk' => 'MK-35', 'nama_mk_matrix' => 'Bahasa Inggris 1 (English for IT/IS)', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.10.2'), 'kode_mk' => 'MK-35', 'nama_mk_matrix' => 'Bahasa Inggris 1 (English for IT/IS)', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.10.3'), 'kode_mk' => 'MK-36', 'nama_mk_matrix' => 'Bahasa Inggris 2 (Prepare for TOEFL)', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 7, 'bobot_uas' => 8, 'skor_maksimal' => 20],
            ['cpmk_id' => $getCpmkId('CPMK.11.1'), 'kode_mk' => 'MK-38', 'nama_mk_matrix' => 'Pendidikan Agama', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.11.1'), 'kode_mk' => 'MK-39', 'nama_mk_matrix' => 'Pendidikan Al Islam', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
            ['cpmk_id' => $getCpmkId('CPMK.11.2'), 'kode_mk' => 'MK-40', 'nama_mk_matrix' => 'Pendidikan Pancasila', 'bobot_partisipatif' => 2, 'bobot_tugas' => 3, 'bobot_uts' => 10, 'bobot_uas' => 10, 'skor_maksimal' => 25],
        ];

        DB::table('matriks_cpmk')->insert($data);
    }
}
