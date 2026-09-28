<?php

namespace Tests\Feature;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Mahasiswa;
use App\Models\MatriksCpmk;
use App\Models\NilaiMahasiswa;
use App\Models\PemetaanMk;
use App\Services\CplCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CplCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CplCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CplCalculationService();
    }

    /**
     * Test hitungUntukMahasiswa dengan data sederhana
     *
     * Setup:
     * - 1 CPL (ambang_minimum = 70)
     * - 1 CPMK
     * - 2 MK dengan bobot berbeda
     * - 1 Mahasiswa dengan nilai tertentu
     *
     * MK1: bobot_partisipatif=2, bobot_tugas=3, bobot_uts=5, bobot_uas=5, skor_maksimal=15
     * MK2: bobot_partisipatif=1, bobot_tugas=1, bobot_uts=1, bobot_uas=2, skor_maksimal=5
     *
     * Nilai MK1: partisipatif=80, tugas=85, uts=90, uts=95
     * Nilai MK2: partisipatif=70, tugas=70, uts=70, uas=70
     */
    public function test_hitung_untuk_mahasiswa_single_cpl()
    {
        // Buat CPL
        $cpl = Cpl::create([
            'kode_cpl' => 'CPL-01',
            'deskripsi' => 'Test CPL',
            'ambang_minimum' => 70.00,
        ]);

        // Buat CPMK
        $cpmk = Cpmk::create([
            'cpl_id' => $cpl->id,
            'kode_cpmk' => 'CPMK.01.1',
            'deskripsi' => 'Test CPMK',
            'ambang_minimum' => 70.00,
        ]);

        // Buat Mahasiswa
        $mahasiswa = Mahasiswa::create([
            'nim' => '25011130001',
            'nama' => 'Test Mahasiswa',
        ]);

        // Buat Matriks CPMK untuk MK1
        $matriks1 = MatriksCpmk::create([
            'cpmk_id' => $cpmk->id,
            'kode_mk' => 'MK001',
            'nama_mk_matrix' => 'Mata Kuliah 1',
            'bobot_partisipatif' => 2,
            'bobot_tugas' => 3,
            'bobot_uts' => 5,
            'bobot_uas' => 5,
            'skor_maksimal' => 15,
        ]);

        // Buat Matriks CPMK untuk MK2
        $matriks2 = MatriksCpmk::create([
            'cpmk_id' => $cpmk->id,
            'kode_mk' => 'MK002',
            'nama_mk_matrix' => 'Mata Kuliah 2',
            'bobot_partisipatif' => 1,
            'bobot_tugas' => 1,
            'bobot_uts' => 1,
            'bobot_uas' => 2,
            'skor_maksimal' => 5,
        ]);

        // Buat Pemetaan MK (terverifikasi)
        $pemetaan1 = PemetaanMk::create([
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'kode_mk' => 'MK001',
            'status_terverifikasi' => true,
        ]);

        $pemetaan2 = PemetaanMk::create([
            'nama_mk_siakad' => 'Mata Kuliah 2',
            'kode_mk' => 'MK002',
            'status_terverifikasi' => true,
        ]);

        // Buat Nilai Mahasiswa
        NilaiMahasiswa::create([
            'nim' => '25011130001',
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'partisipatif' => 80,
            'tugas' => 85,
            'uts' => 90,
            'uas' => 95,
            'nilai_akhir' => 88,
        ]);

        NilaiMahasiswa::create([
            'nim' => '25011130001',
            'nama_mk_siakad' => 'Mata Kuliah 2',
            'partisipatif' => 70,
            'tugas' => 70,
            'uts' => 70,
            'uas' => 70,
            'nilai_akhir' => 70,
        ]);

        // Hitung
        $results = $this->service->hitungUntukMahasiswa('25011130001');

        // Verifikasi hasil
        $this->assertNotEmpty($results);

        // Ambil hasil CPL-01
        $resultCpl = $results->first();

        // Manual calculation:
        // MK1: (80/100)*2 + (85/100)*3 + (90/100)*5 + (95/100)*5 = 1.6 + 2.55 + 4.5 + 4.75 = 13.4
        // MK2: (70/100)*1 + (70/100)*1 + (70/100)*1 + (70/100)*2 = 0.7 + 0.7 + 0.7 + 1.4 = 3.5
        //
        // Total skor tercapai = 13.4 + 3.5 = 16.9
        // Total skor maksimal = 15 + 5 = 20
        // % Capaian = 16.9 / 20 * 100 = 84.5%

        $this->assertEquals('CPL-01', $resultCpl['kode_cpl']);
        $this->assertEquals(84.50, $resultCpl['persen_capaian']);
        $this->assertTrue($resultCpl['status_tercapai']); // 84.5 >= 70
        $this->assertEquals(2, $resultCpl['jumlah_mk_dipakai']);
    }

    /**
     * Test hitungUntukMahasiswa - mahasiswa tidak ditemukan
     */
    public function test_hitung_untuk_mahasiswa_tidak_ditemukan()
    {
        $results = $this->service->hitungUntukMahasiswa('NONEXISTENT');

        $this->assertEmpty($results);
    }

    /**
     * Test hitungSkorCpmk - pemetaan tidak terverifikasi diabaikan
     */
    public function test_pemetaan_belum_terverifikasi_diabaikan()
    {
        // Buat data
        $cpl = Cpl::create([
            'kode_cpl' => 'CPL-01',
            'deskripsi' => 'Test CPL',
            'ambang_minimum' => 70.00,
        ]);

        $cpmk = Cpmk::create([
            'cpl_id' => $cpl->id,
            'kode_cpmk' => 'CPMK.01.1',
            'deskripsi' => 'Test CPMK',
            'ambang_minimum' => 70.00,
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => '25011130001',
            'nama' => 'Test Mahasiswa',
        ]);

        MatriksCpmk::create([
            'cpmk_id' => $cpmk->id,
            'kode_mk' => 'MK001',
            'nama_mk_matrix' => 'Mata Kuliah 1',
            'bobot_partisipatif' => 2,
            'bobot_tugas' => 3,
            'bobot_uts' => 5,
            'bobot_uas' => 5,
            'skor_maksimal' => 15,
        ]);

        // Pemetaan BELUM terverifikasi
        PemetaanMk::create([
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'kode_mk' => 'MK001',
            'status_terverifikasi' => false, // <-- FALSE
        ]);

        NilaiMahasiswa::create([
            'nim' => '25011130001',
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'partisipatif' => 100,
            'tugas' => 100,
            'uts' => 100,
            'uas' => 100,
            'nilai_akhir' => 100,
        ]);

        // Hitung
        $result = $this->service->hitungSkorCpmk('25011130001', $cpmk);

        // Karena pemetaan tidak terverifikasi, skor harus 0
        $this->assertEquals(0, $result['skor_tercapai']);
        $this->assertEquals(0, $result['skor_maksimal']);
        $this->assertEquals(0, $result['persen_capaian']);
    }

    /**
     * Test detailCpmk
     */
    public function test_detail_cpmk()
    {
        $cpl = Cpl::create([
            'kode_cpl' => 'CPL-01',
            'deskripsi' => 'Test CPL',
            'ambang_minimum' => 70.00,
        ]);

        $cpmk1 = Cpmk::create([
            'cpl_id' => $cpl->id,
            'kode_cpmk' => 'CPMK.01.1',
            'deskripsi' => 'Test CPMK 1',
            'ambang_minimum' => 70.00,
        ]);

        $cpmk2 = Cpmk::create([
            'cpl_id' => $cpl->id,
            'kode_cpmk' => 'CPMK.01.2',
            'deskripsi' => 'Test CPMK 2',
            'ambang_minimum' => 70.00,
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => '25011130001',
            'nama' => 'Test Mahasiswa',
        ]);

        // CPMK1 punya matriks
        MatriksCpmk::create([
            'cpmk_id' => $cpmk1->id,
            'kode_mk' => 'MK001',
            'nama_mk_matrix' => 'Mata Kuliah 1',
            'bobot_partisipatif' => 10,
            'bobot_tugas' => 10,
            'bobot_uts' => 10,
            'bobot_uas' => 10,
            'skor_maksimal' => 40,
        ]);

        // CPMK2 TIDAK punya matriks

        PemetaanMk::create([
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'kode_mk' => 'MK001',
            'status_terverifikasi' => true,
        ]);

        NilaiMahasiswa::create([
            'nim' => '25011130001',
            'nama_mk_siakad' => 'Mata Kuliah 1',
            'partisipatif' => 100,
            'tugas' => 100,
            'uts' => 100,
            'uas' => 100,
            'nilai_akhir' => 100,
        ]);

        $details = $this->service->detailCpmk('25011130001', $cpl->id);

        // Harus ada 2 CPMK
        $this->assertCount(2, $details);

        // CPMK1 harus ada skor
        $cpmk1Result = $details->firstWhere('kode_cpmk', 'CPMK.01.1');
        $this->assertEquals(40, $cpmk1Result['skor_tercapai']);
        $this->assertEquals(40, $cpmk1Result['skor_maksimal']);
        $this->assertEquals(100, $cpmk1Result['persen_capaian']);

        // CPMK2 harus 0 (tidak ada matriks)
        $cpmk2Result = $details->firstWhere('kode_cpmk', 'CPMK.01.2');
        $this->assertEquals(0, $cpmk2Result['skor_tercapai']);
        $this->assertEquals(0, $cpmk2Result['skor_maksimal']);
    }

    /**
     * Test getJumlahBelumTerverifikasi
     */
    public function test_get_jumlah_belum_terverifikasi()
    {
        PemetaanMk::create([
            'nama_mk_siakad' => 'MK A',
            'status_terverifikasi' => true,
        ]);

        PemetaanMk::create([
            'nama_mk_siakad' => 'MK B',
            'status_terverifikasi' => false,
        ]);

        PemetaanMk::create([
            'nama_mk_siakad' => 'MK C',
            'status_terverifikasi' => false,
        ]);

        $this->assertEquals(2, $this->service->getJumlahBelumTerverifikasi());
    }
}
