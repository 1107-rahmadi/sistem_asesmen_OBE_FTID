<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\CplCalculationService;
use Illuminate\Console\Command;

class HitungCapaianCpl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cpl:hitung
                            {--nim= : NIM mahasiswa spesifik (kosongkan untuk semua)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung capaian CPL mahasiswa';

    /**
     * Execute the console command.
     */
    public function handle(CplCalculationService $service): int
    {
        $nim = $this->option('nim');

        if ($nim) {
            return $this->hitungSingle($nim, $service);
        }

        return $this->hitungSemua($service);
    }

    /**
     * Hitung untuk satu mahasiswa
     */
    private function hitungSingle(string $nim, CplCalculationService $service): int
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (!$mahasiswa) {
            $this->error("Mahasiswa dengan NIM {$nim} tidak ditemukan.");
            return Command::FAILURE;
        }

        $this->info("Menghitung capaian CPL untuk NIM: {$nim} ({$mahasiswa->nama})...");

        try {
            $results = $service->simpanCapaian($nim);

            $this->info("Berhasil! Hasil perhitungan:");
            $this->newLine();

            $header = ['Kode CPL', '% Capaian', 'Status', 'MK Dipakai'];
            $rows = [];

            foreach ($results as $result) {
                $cpl = $result->cpl;
                $persen = number_format($result->persen_capaian, 2);
                $status = $result->status_tercapai ? 'TERCAPAI' : 'BELUM';
                $mk = $result->jumlah_mk_dipakai;

                $rows[] = [$cpl->kode_cpl, "{$persen}%", $status, $mk];
            }

            $this->table($header, $rows);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Gagal menghitung: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Hitung untuk semua mahasiswa dengan progress bar
     */
    private function hitungSemua(CplCalculationService $service): int
    {
        $totalMahasiswa = Mahasiswa::count();

        if ($totalMahasiswa === 0) {
            $this->warn("Tidak ada mahasiswa di database.");
            return Command::SUCCESS;
        }

        $this->info("Menghitung capaian CPL untuk {$totalMahasiswa} mahasiswa...");

        $bar = $this->output->createProgressBar($totalMahasiswa);
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        Mahasiswa::chunk(100, function ($mahasiswas) use ($service, $bar, &$successCount, &$errorCount) {
            foreach ($mahasiswas as $mahasiswa) {
                try {
                    $service->simpanCapaian($mahasiswa->nim);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Selesai!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Mahasiswa', $totalMahasiswa],
                ['Berhasil', $successCount],
                ['Gagal', $errorCount],
            ]
        );

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
