<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapaianCplMahasiswa;
use App\Models\Mahasiswa;
use App\Services\CplCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CapaianCplController extends Controller
{
    protected $service;

    public function __construct(CplCalculationService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of CPL achievements per mahasiswa.
     */
    public function index(Request $request)
    {
        // Filter & Search
        $nim = $request->input('nim');
        $nama = $request->input('nama');

        $query = Mahasiswa::query();

        if ($nim) {
            $query->where('nim', 'like', '%' . $nim . '%');
        }

        if ($nama) {
            $query->where('nama', 'like', '%' . $nama . '%');
        }

        $data['mahasiswas'] = $query->orderBy('nama')->paginate(20);
        $data['nim'] = $nim;
        $data['nama'] = $nama;

        // Ambil data capaian_cpl_mahasiswa untuk semua NIM yang ditampilkan
        $nimList = $data['mahasiswas']->pluck('nim')->toArray();
        $capaianByNim = CapaianCplMahasiswa::with('cpl')
            ->whereIn('nim', $nimList)
            ->get()
            ->groupBy('nim');

        $data['capaianByNim'] = $capaianByNim;
        $data['lastCalculated'] = Cache::get('cpl_last_calculated');

        // Cek pemetaan MK yang belum terverifikasi
        $data['belumTerverifikasi'] = $this->service->getJumlahBelumTerverifikasi();

        return view('admin.capaian_cpl.index', $data);
    }

    /**
     * Hitung ulang capaian CPL
     */
    public function recalculate(Request $request)
    {
        $nim = $request->input('nim');

        try {
            if ($nim) {
                // Hitung untuk satu mahasiswa
                $this->service->simpanCapaian($nim);
                $message = "Capaian CPL untuk NIM {$nim} berhasil dihitung ulang.";
            } else {
                // Hitung untuk semua mahasiswa
                $result = $this->service->hitungSemua();
                $message = "Berhasil menghitung {$result['processed']} dari {$result['total_mahasiswa']} mahasiswa.";
            }

            // Simpan timestamp terakhir dihitung
            Cache::put('cpl_last_calculated', now()->format('Y-m-d H:i:s'), now()->addMonth());

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung: ' . $e->getMessage());
        }
    }

    /**
     * Detail capaian CPL per mahasiswa (untuk AJAX/modal)
     */
    public function detail(string $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->firstOrFail();
        $cplResults = $this->service->hitungUntukMahasiswa($nim);

        return view('admin.capaian_cpl.detail', [
            'mahasiswa' => $mahasiswa,
            'cplResults' => $cplResults,
        ]);
    }

    /**
     * Get chart data untuk radar chart (API endpoint)
     * Mengembalikan data % capaian CPL dalam format siap Chart.js
     */
    public function getChartData(string $nim)
    {
        $cplResults = $this->service->hitungUntukMahasiswa($nim);

        if ($cplResults->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        // Format data untuk Chart.js radar chart
        $chartData = $cplResults
            ->sortBy('kode_cpl')
            ->map(function ($item) {
                return [
                    'kode_cpl' => $item['kode_cpl'],
                    'deskripsi' => $item['deskripsi'],
                    'ambang_minimum' => (float) $item['ambang_minimum'],
                    'persen_capaian' => (float) $item['persen_capaian'],
                    'status_tercapai' => $item['status_tercapai'],
                    'jumlah_mk_dipakai' => $item['jumlah_mk_dipakai'],
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'nim' => $nim,
            'labels' => collect($chartData)->pluck('kode_cpl')->toArray(),
            'datasets' => [
                [
                    'label' => 'Capaian Mahasiswa',
                    'data' => collect($chartData)->pluck('persen_capaian')->toArray(),
                    'backgroundColor' => 'rgba(52, 152, 219, 0.2)',
                    'borderColor' => 'rgba(52, 152, 219, 1)',
                    'borderWidth' => 2,
                    'pointBackgroundColor' => collect($chartData)->map(function ($item) {
                        return $item['status_tercapai'] ? 'rgba(40, 167, 69, 1)' : 'rgba(255, 99, 132, 1)';
                    })->toArray(),
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(52, 152, 219, 1)',
                ],
                [
                    'label' => 'Ambang Minimum',
                    'data' => collect($chartData)->pluck('ambang_minimum')->toArray(),
                    'backgroundColor' => 'rgba(149, 165, 166, 0.1)',
                    'borderColor' => 'rgba(149, 165, 166, 0.8)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'pointBackgroundColor' => 'rgba(149, 165, 166, 0)',
                    'pointBorderColor' => 'rgba(149, 165, 166, 0)',
                    'pointHoverBackgroundColor' => 'rgba(149, 165, 166, 0)',
                    'pointHoverBorderColor' => 'rgba(149, 165, 166, 0)',
                ],
            ],
            'detail' => $chartData,
        ]);
    }
}
