<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CplSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'kode_cpl' => 'CPL-01',
                'deskripsi' => 'Mampu memahami konsep manajemen proyek untuk pengembangan sistem informasi, perancangan UI/UX, dan penggunaan data yang legal sesuai dengan kebutuhan organisasi serta keamanannya.',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-02',
                'deskripsi' => 'Mampu mengidentifikasi dan menjelaskan peranan konsep dasar sistem informasi, dasar logika, basis data, struktur data, dan infrastruktur TI dalam pengembangan sistem informasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-03',
                'deskripsi' => 'Mampu mengembangkan sistem informasi dengan menerapkan dasar logika, struktur data, basis data, dan pengolahan data, merencanakan infrastruktur TI sesuai dengan kebutuhan organisasi.',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-04',
                'deskripsi' => 'Mampu mengidentifikasi dan mengkaji metodologi manajemen proyek untuk pengembangan sistem informasi dan penggunaan data yang legal sesuai dengan kebutuhan organisasi.',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-05',
                'deskripsi' => 'Mampu mengoperasikan sistem informasi dengan menggunakan tools, teknik, dan proses manajemen proyek serta memahami teknik UI/UX berdasarkan metodologi yang tepat sesuai dengan karakteristik kebutuhan organisasi.',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-06',
                'deskripsi' => 'Mampu memahami dan menggunakan berbagai metodologi pengembangan sistem beserta alat pemodelan sistem dan menganalisa kebutuhan pengguna dalam membangun sistem informasi untuk mencapai tujuan organisasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-07',
                'deskripsi' => 'Mampu membuat perencanaan infrastruktur TI, arsitektur jaringan dan layanan fisik sesuai dengan kebutuhan organisasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-08',
                'deskripsi' => 'Mampu memahami konsep-konsep bahasa pemrograman dan pengolahan data informasi, serta menerapkannya pada program aplikasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-09',
                'deskripsi' => 'Mampu menciptakan peluang usaha dan melakukan pengelolaan bisnis dengan memanfaatkan teknologi informasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-10',
                'deskripsi' => 'Mampu menggunakan bahasa Indonesia dan bahasa Inggris yang baik dan benar yang terkait karya ilmiah bidang Sistem Informasi',
                'ambang_minimum' => 70.00,
            ],
            [
                'kode_cpl' => 'CPL-11',
                'deskripsi' => 'Mampu menunjukkan sikap Bertakwa kepada Tuhan Yang Maha Esa, taat hukum, dan disiplin dalam kehidupan bermasyarakat dan bernegara.',
                'ambang_minimum' => 70.00,
            ],
        ];

        DB::table('cpl')->insert($data);
    }
}
