@extends('admin.layouts.app')

@section('title', 'Detail Pemetaan MK')

@section('page-title', 'Pemetaan MK')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-eye"></i> Detail Pemetaan MK</h5>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">Nama MK (Siakad)</th>
                <td>: {{ $pemetaanMk->nama_mk_siakad }}</td>
            </tr>
            <tr>
                <th>Kode MK</th>
                <td>: {{ $pemetaanMk->kode_mk ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>:
                    @if($pemetaanMk->status_terverifikasi)
                        <span class="badge bg-success">Terverifikasi</span>
                    @else
                        <span class="badge bg-warning">Belum Terverifikasi</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Catatan</th>
                <td>: {{ $pemetaanMk->catatan ?? '-' }}</td>
            </tr>
        </table>

        <hr>

        <h6 class="mb-3"><i class="fas fa-link"></i> Matriks CPMK Terkait</h6>
        @if($matriksRelated->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>CPMK</th>
                            <th>CPL</th>
                            <th>Part</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matriksRelated as $matriks)
                        <tr>
                            <td><span class="badge bg-info">{{ $matriks->cpmk->kode_cpmk }}</span></td>
                            <td>{{ $matriks->cpmk->cpl->kode_cpl ?? '-' }}</td>
                            <td class="text-center">{{ $matriks->bobot_partisipatif }}</td>
                            <td class="text-center">{{ $matriks->bobot_tugas }}</td>
                            <td class="text-center">{{ $matriks->bobot_uts }}</td>
                            <td class="text-center">{{ $matriks->bobot_uas }}</td>
                            <td class="text-center"><strong>{{ $matriks->skor_maksimal }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-secondary mb-0">
                <i class="fas fa-info-circle"></i> Tidak ada matriks CPMK yang terkait dengan MK ini.
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('admin.pemetaan-mk.edit', $pemetaanMk->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.pemetaan-mk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
