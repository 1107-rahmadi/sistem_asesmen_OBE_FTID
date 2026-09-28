@extends('admin.layouts.app')

@section('title', 'Capaian CPL')

@section('page-title', 'Capaian CPL')

@push('styles')
<style>
    .progress-mini {
        height: 8px;
        font-size: 10px;
    }
    .cpl-card {
        border-left: 4px solid #3498db;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    .cpl-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .cpl-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 15px;
        border-radius: 8px 8px 0 0;
    }
    .cpl-body {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 0 0 8px 8px;
    }
    .cpmk-section {
        background: white;
        border-radius: 6px;
        padding: 12px;
        margin-top: 10px;
        border: 1px solid #e9ecef;
    }
    .cpmk-header {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #e9ecef;
        border-radius: 4px;
        margin-bottom: 8px;
    }
    .cpmk-header:hover {
        background: #dee2e6;
    }
    .mk-row {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        margin-bottom: 6px;
        font-size: 13px;
    }
    .badge-success-custom {
        background-color: #28a745;
        color: white;
    }
    .badge-danger-custom {
        background-color: #dc3545;
        color: white;
    }
    .persen-display {
        font-weight: 700;
        font-size: 1.1rem;
    }
    .nim-box {
        background: #e9ecef;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .chart-container {
        position: relative;
        max-width: 500px;
        margin: 0 auto 20px;
    }
    .chart-card {
        border: 2px solid #3498db;
        border-radius: 10px;
        overflow: hidden;
    }
    .chart-header {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        padding: 10px 15px;
        font-weight: 600;
    }
    .chart-body {
        background: white;
        padding: 15px;
    }
    .no-data-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')

{{-- Banner peringatan jika ada pemetaan MK belum terverifikasi --}}
@if($belumTerverifikasi > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>{{ $belumTerverifikasi }}</strong> mata kuliah belum terverifikasi pemetaannya.
    Capaian CPL di bawah ini mungkin belum lengkap.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Header Section --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Capaian CPL per Mahasiswa</h5>
        <small class="text-muted">
            @if($lastCalculated)
                Terakhir dihitung: {{ $lastCalculated }}
            @else
                Belum pernah dihitung
            @endif
        </small>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.capaian-cpl.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="nim" class="form-control" placeholder="Filter NIM..." value="{{ $nim ?? '' }}" style="width: 140px;">
            <input type="text" name="nama" class="form-control" placeholder="Filter Nama..." value="{{ $nama ?? '' }}" style="width: 180px;">
            <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
            @if($nim || $nama)
                <a href="{{ route('admin.capaian-cpl.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
        <form action="{{ route('admin.capaian-cpl.recalculate') }}" method="POST">
            @csrf
            <input type="hidden" name="nim" value="{{ $nim ?? '' }}">
            <button type="submit" class="btn btn-primary" onclick="return confirm('Hitung ulang capaian CPL?')">
                <i class="fas fa-calculator me-1"></i> Hitung Ulang
            </button>
        </form>
    </div>
</div>

{{-- Data Mahasiswa --}}
@forelse($mahasiswas as $mhs)
@php
    $cplList = $capaianByNim->get($mhs->nim, collect());
    $avgCapaian = $cplList->count() > 0 ? $cplList->avg('persen_capaian') : 0;
    $tercapainya = $cplList->where('status_tercapai', true)->count();
    $totalnya = $cplList->count();
@endphp

<div class="nim-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="fas fa-user me-2"></i>{{ $mhs->nama }}</h5>
            <small class="text-muted">NIM: {{ $mhs->nim }}</small>
            @if($mhs->kelas_raw)
                <span class="badge bg-secondary ms-2">{{ $mhs->kelas_raw }}</span>
            @endif
        </div>
        <div class="text-end">
            <div class="persen-display">
                Rata-rata: <span class="text-primary">{{ number_format($avgCapaian, 1) }}%</span>
            </div>
            <small class="text-muted">
                {{ $tercapainya }}/{{ $totalnya }} CPL TERCAPAI
            </small>
        </div>
    </div>

    {{-- Progress Bar Rata-rata --}}
    <div class="progress progress-mini" style="height: 10px;">
        <div class="progress-bar bg-primary" role="progressbar"
             style="width: {{ min(100, $avgCapaian) }}%;"
             aria-valuenow="{{ $avgCapaian }}" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- RADAR CHART - Peta Capaian CPL --}}
{{-- ========================================== --}}
<div class="chart-card mb-4" id="chartCard{{ $mhs->id }}">
    <div class="chart-header">
        <i class="fas fa-spider me-2"></i>Peta Capaian CPL
    </div>
    <div class="chart-body">
        <div class="chart-container">
            {{-- Chart canvas will be rendered here --}}
            <canvas id="radarChart{{ $mhs->id }}"></canvas>
        </div>
        <div id="noDataChart{{ $mhs->id }}" class="no-data-chart d-none">
            <i class="fas fa-chart-line fa-3x mb-3"></i>
            <p class="mb-0">Belum ada data capaian.</p>
            <small>Klik "Hitung Ulang" terlebih dahulu untuk menghitung.</small>
        </div>
    </div>
</div>

{{-- List CPL --}}
@if($cplList->count() > 0)
    @foreach($cplList as $capaian)
    <div class="cpl-card">
        <div class="cpl-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $capaian->cpl->kode_cpl ?? '-' }}</strong>
                    <button class="btn btn-sm btn-light ms-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#detail{{ $mhs->id }}{{ $capaian->cpl_id }}" aria-expanded="false">
                        <i class="fas fa-chevron-down"></i> Detail
                    </button>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="persen-display">{{ number_format($capaian->persen_capaian, 1) }}%</span>
                    @if($capaian->status_tercapai)
                        <span class="badge badge-success-custom"><i class="fas fa-check me-1"></i>TERCAPAI</span>
                    @else
                        <span class="badge badge-danger-custom"><i class="fas fa-times me-1"></i>BELUM</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="cpl-body">
            {{-- Progress Bar CPL --}}
            <div class="progress mb-3" style="height: 14px;">
                @php
                    $barClass = $capaian->status_tercapai ? 'bg-success' : 'bg-warning';
                @endphp
                <div class="progress-bar {{ $barClass }}" role="progressbar"
                     style="width: {{ min(100, $capaian->persen_capaian) }}%;"
                     aria-valuenow="{{ $capaian->persen_capaian }}" aria-valuemin="0" aria-valuemax="100">
                    {{ number_format($capaian->persen_capaian, 1) }}%
                </div>
            </div>

            {{-- Deskripsi CPL --}}
            <p class="mb-2 text-muted" style="font-size: 13px;">
                <i class="fas fa-info-circle me-1"></i>
                {{ Str::limit($capaian->cpl->deskripsi ?? 'Tidak ada deskripsi', 200) }}
            </p>

            <small class="text-muted">
                <i class="fas fa-book me-1"></i>
                Ambang Minimum: {{ number_format($capaian->cpl->ambang_minimum ?? 0, 0) }}% |
                MK Dipakai: {{ $capaian->jumlah_mk_dipakai }}
            </small>

            {{-- Detail CPMK (Collapsible) --}}
            <div class="collapse" id="detail{{ $mhs->id }}{{ $capaian->cpl_id }}">
                <div class="cpmk-section">
                    <h6 class="mb-3"><i class="fas fa-list-check me-2"></i>Rincian per CPMK</h6>

                    {{-- Load detail dari service --}}
                    @php
                        $service = app(\App\Services\CplCalculationService::class);
                        $detailCpmk = $service->detailCpmk($mhs->nim, $capaian->cpl_id);
                    @endphp

                    @forelse($detailCpmk as $cpmk)
                    <div class="cpmk-header" onclick="toggleCpmk(this)">
                        <div>
                            <strong>{{ $cpmk['kode_cpmk'] }}</strong>
                            <span class="badge bg-info ms-2">{{ number_format($cpmk['persen_capaian'], 1) }}%</span>
                        </div>
                        <div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="cpmk-details d-none">
                        <p class="text-muted mb-2" style="font-size: 12px;">
                            {{ Str::limit($cpmk['deskripsi'] ?? 'Tidak ada deskripsi', 150) }}
                        </p>

                        @forelse($cpmk['mk_details'] as $mk)
                        <div class="mk-row">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $mk['kode_mk'] }}</strong>
                                    <span class="text-muted"> - {{ $mk['nama_mk'] }}</span>
                                </div>
                                <div>
                                    <span class="badge bg-secondary">Skor: {{ $mk['skor_tercapai'] }}/{{ $mk['skor_maksimal'] }}</span>
                                </div>
                            </div>
                            <div class="mt-1" style="font-size: 11px;">
                                Part: {{ $mk['komponen']['partisipatif']['nilai'] ?? '-' }}
                                | Tug: {{ $mk['komponen']['tugas']['nilai'] ?? '-' }}
                                | UTS: {{ $mk['komponen']['uts']['nilai'] ?? '-' }}
                                | UAS: {{ $mk['komponen']['uas']['nilai'] ?? '-' }}
                            </div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-2">Tidak ada MK yang mendukung CPMK ini</div>
                        @endforelse
                    </div>
                    @empty
                    <div class="text-muted text-center py-3">Tidak ada CPMK untuk CPL ini</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-secondary text-center">
        <i class="fas fa-info-circle me-2"></i>
        Belum ada data capaian CPL. Klik "Hitung Ulang" untuk menghitung.
    </div>
@endif

<hr class="my-4">
@empty
<div class="text-center py-5">
    <i class="fas fa-users fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Belum ada data mahasiswa</h5>
    <p class="text-muted">Tambahkan data mahasiswa terlebih dahulu</p>
</div>
@endforelse

{{-- Pagination --}}
@if($mahasiswas->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $mahasiswas->links() }}
</div>
@endif

@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Store chart instances
const radarCharts = {};

// Load radar chart data for each mahasiswa
@forelse($mahasiswas as $mhs)
(async function() {
    const nim = '{{ $mhs->nim }}';
    const cardId = {{ $mhs->id }};

    try {
        const response = await fetch(`/admin/capaian-cpl/${nim}/chart`);
        const data = await response.json();

        if (data.error || !data.labels || data.labels.length === 0) {
            document.getElementById(`noDataChart${cardId}`).classList.remove('d-none');
            document.getElementById(`radarChart${cardId}`).classList.add('d-none');
            return;
        }

        // Build custom tooltip
        const originalLabels = data.labels;
        const detailData = data.detail;

        // Create radar chart
        const ctx = document.getElementById(`radarChart${cardId}`).getContext('2d');

        radarCharts[cardId] = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        min: 0,
                        ticks: {
                            stepSize: 20,
                            backdropColor: 'transparent',
                            font: { size: 10 }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        angleLines: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        pointLabels: {
                            font: { size: 11, weight: 'bold' },
                            color: '#2c3e50'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const idx = context[0].dataIndex;
                                return detailData[idx].kode_cpl;
                            },
                            label: function(context) {
                                const idx = context.dataIndex;
                                const detail = detailData[idx];
                                const desc = detail.deskripsi.length > 60
                                    ? detail.deskripsi.substring(0, 60) + '...'
                                    : detail.deskripsi;
                                const status = detail.status_tercapai ? 'Tercapai' : 'Belum Tercapai';
                                const color = detail.status_tercapai ? '#28a745' : '#dc3545';

                                if (context.datasetIndex === 0) {
                                    return [
                                        `Deskripsi: ${desc}`,
                                        `Capaian: ${detail.persen_capaian.toFixed(1)}%`,
                                        `Status: ${status}`,
                                        `MK Dipakai: ${detail.jumlah_mk_dipakai}`
                                    ];
                                }
                                return `Ambang Minimum: ${detail.ambang_minimum.toFixed(0)}%`;
                            }
                        },
                        backgroundColor: 'rgba(44, 62, 80, 0.9)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 6
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading radar chart:', error);
        document.getElementById(`noDataChart${cardId}`).classList.remove('d-none');
        document.getElementById(`radarChart${cardId}`).classList.add('d-none');
    }
})();
@empty
{{-- No mahasiswa --}}
@endforelse

function toggleCpmk(header) {
    const details = header.nextElementSibling;
    const icon = header.querySelector('i.fas');

    if (details.classList.contains('d-none')) {
        details.classList.remove('d-none');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        details.classList.add('d-none');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endpush
