<?php

namespace App\Services;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\CapaianCplMahasiswa;
use App\Models\Mahasiswa;
use App\Models\MatriksCpmk;
use App\Models\NilaiMahasiswa;
use App\Models\PemetaanMk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CplCalculationService
{
    /**
     * Hitung semua CPL untuk satu mahasiswa
     *
     * @param string $nim
     * @return Collection
     */
    public function hitungUntukMahasiswa(string $nim): Collection
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        if (!$mahasiswa) {
            return collect();
        }

        $cpls = Cpl::with('cpmk')->get();
        $results = collect();

        foreach ($cpls as $cpl) {
            $detailCpmk = $this->detailCpmk($nim, $cpl->id);

            $totalSkorTercapai = $detailCpmk->sum('skor_tercapai');
            $totalSkorMaksimal = $detailCpmk->sum('skor_maksimal');

            $persenCapaian = $totalSkorMaksimal > 0
                ? round(($totalSkorTercapai / $totalSkorMaksimal) * 100, 2)
                : 0;

            $jumlahMkDipakai = $detailCpmk->pluck('nama_mk')->filter()->unique()->count();

            $results->push([
                'cpl_id' => $cpl->id,
                'kode_cpl' => $cpl->kode_cpl,
                'deskripsi' => $cpl->deskripsi,
                'ambang_minimum' => $cpl->ambang_minimum,
                'persen_capaian' => $persenCapaian,
                'status_tercapai' => $persenCapaian >= $cpl->ambang_minimum,
                'jumlah_mk_dipakai' => $jumlahMkDipakai,
                'detail_cpmk' => $detailCpmk,
            ]);
        }

        return $results;
    }

    /**
     * Hitung dan simpan ke tabel capaian_cpl_mahasiswa untuk semua mahasiswa
     *
     * @return array Statistics
     */
    public function hitungSemua(): array
    {
        $mahasiswas = Mahasiswa::all();
        $totalMahasiswa = $mahasiswas->count();
        $processed = 0;
        $errors = [];

        foreach ($mahasiswas as $mahasiswa) {
            try {
                $this->simpanCapaian($mahasiswa->nim);
                $processed++;
            } catch (\Exception $e) {
                $errors[] = [
                    'nim' => $mahasiswa->nim,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total_mahasiswa' => $totalMahasiswa,
            'processed' => $processed,
            'errors' => $errors,
        ];
    }

    /**
     * Simpan capaian CPL untuk satu mahasiswa ke database
     *
     * @param string $nim
     * @param Carbon|null $timestamp
     * @return Collection
     */
    public function simpanCapaian(string $nim, $timestamp = null): Collection
    {
        $timestamp = $timestamp ?? now();
        $cplResults = $this->hitungUntukMahasiswa($nim);
        $results = collect();

        foreach ($cplResults as $result) {
            $capaian = CapaianCplMahasiswa::updateOrCreate(
                [
                    'nim' => $nim,
                    'cpl_id' => $result['cpl_id'],
                ],
                [
                    'persen_capaian' => $result['persen_capaian'],
                    'jumlah_mk_dipakai' => $result['jumlah_mk_dipakai'],
                    'status_tercapai' => $result['status_tercapai'],
                    'dihitung_pada' => $timestamp,
                ]
            );

            $results->push($capaian);
        }

        return $results;
    }

    /**
     * Detail per CPMK untuk drill-down
     *
     * @param string $nim
     * @param int $cplId
     * @return Collection
     */
    public function detailCpmk(string $nim, int $cplId): Collection
    {
        $cpmks = Cpmk::where('cpl_id', $cplId)->get();
        $results = collect();

        foreach ($cpmks as $cpmk) {
            $detail = $this->hitungSkorCpmk($nim, $cpmk);
            $results->push($detail);
        }

        return $results;
    }

    /**
     * Hitung skor tercapai untuk satu CPMK
     *
     * @param string $nim
     * @param Cpmk $cpmk
     * @return array
     */
    public function hitungSkorCpmk(string $nim, Cpmk $cpmk): array
    {
        // Ambil matriks_cpmk yang terkait CPMK ini
        // JOIN dengan pemetaan_mk (hanya yang terverifikasi)
        // JOIN dengan nilai_mahasiswa
        $matriksData = DB::table('matriks_cpmk as mc')
            ->join('pemetaan_mk as pm', 'pm.nama_mk_siakad', '=', 'mc.nama_mk_matrix')
            ->join('nilai_mahasiswa as nm', function ($join) use ($nim) {
                $join->on('nm.nama_mk_siakad', '=', 'pm.nama_mk_siakad')
                    ->where('nm.nim', '=', $nim);
            })
            ->where('mc.cpmk_id', '=', $cpmk->id)
            ->where('pm.status_terverifikasi', '=', true)
            ->select([
                'mc.kode_mk',
                'mc.nama_mk_matrix',
                'mc.bobot_partisipatif',
                'mc.bobot_tugas',
                'mc.bobot_uts',
                'mc.bobot_uas',
                'mc.skor_maksimal',
                'nm.tugas',
                'nm.uts',
                'nm.uas',
                'nm.partisipatif',
                'nm.nilai_akhir',
            ])
            ->get();

        $skorTercapai = 0;
        $totalSkorMaksimal = 0;
        $mkDetails = [];

        foreach ($matriksData as $row) {
            // Hitung skor tercapai: SUM((nilai_komponen/100) * bobot_komponen)
            $bobotTotal = $row->bobot_partisipatif + $row->bobot_tugas + $row->bobot_uts + $row->bobot_uas;

            if ($bobotTotal > 0) {
                $nilaiPartisipatif = $row->partisipatif ?? 0;
                $nilaiTugas = $row->tugas ?? 0;
                $nilaiUts = $row->uts ?? 0;
                $nilaiUas = $row->uas ?? 0;

                // Hitung komponen
                $skorPartisipatif = ($nilaiPartisipatif / 100) * $row->bobot_partisipatif;
                $skorTugas = ($nilaiTugas / 100) * $row->bobot_tugas;
                $skorUts = ($nilaiUts / 100) * $row->bobot_uts;
                $skorUas = ($nilaiUas / 100) * $row->bobot_uas;

                $skorTercapaiRow = $skorPartisipatif + $skorTugas + $skorUts + $skorUas;
                $skorTercapai += $skorTercapaiRow;
                $totalSkorMaksimal += $row->skor_maksimal;

                $mkDetails[] = [
                    'kode_mk' => $row->kode_mk,
                    'nama_mk' => $row->nama_mk_matrix,
                    'nilai_akhir' => $row->nilai_akhir,
                    'bobot_total' => $bobotTotal,
                    'skor_tercapai' => round($skorTercapaiRow, 2),
                    'skor_maksimal' => $row->skor_maksimal,
                    'komponen' => [
                        'partisipatif' => ['bobot' => $row->bobot_partisipatif, 'nilai' => $nilaiPartisipatif, 'skor' => round($skorPartisipatif, 2)],
                        'tugas' => ['bobot' => $row->bobot_tugas, 'nilai' => $nilaiTugas, 'skor' => round($skorTugas, 2)],
                        'uts' => ['bobot' => $row->bobot_uts, 'nilai' => $nilaiUts, 'skor' => round($skorUts, 2)],
                        'uas' => ['bobot' => $row->bobot_uas, 'nilai' => $nilaiUas, 'skor' => round($skorUas, 2)],
                    ],
                ];
            }
        }

        $persenCapaian = $totalSkorMaksimal > 0
            ? round(($skorTercapai / $totalSkorMaksimal) * 100, 2)
            : 0;

        return [
            'cpmk_id' => $cpmk->id,
            'kode_cpmk' => $cpmk->kode_cpmk,
            'deskripsi' => $cpmk->deskripsi,
            'ambang_minimum' => $cpmk->ambang_minimum,
            'skor_tercapai' => round($skorTercapai, 2),
            'skor_maksimal' => $totalSkorMaksimal,
            'persen_capaian' => $persenCapaian,
            'mk_details' => $mkDetails,
            'nama_mk' => collect($mkDetails)->pluck('nama_mk')->filter()->values()->toArray(),
        ];
    }

    /**
     * Cek apakah ada pemetaan MK yang belum terverifikasi
     *
     * @return int
     */
    public function getJumlahBelumTerverifikasi(): int
    {
        return PemetaanMk::where('status_terverifikasi', false)->count();
    }
}
