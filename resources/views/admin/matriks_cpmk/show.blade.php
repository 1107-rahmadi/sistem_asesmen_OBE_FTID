@extends('admin.layouts.app')

@section('title', 'Detail Matriks CPMK')

@section('page-title', 'Matriks CPMK - Detail')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detail Matriks CPMK</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.matriks-cpmk.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Kode MK</th>
                        <td><strong>{{ $matriksCpmk->kode_mk }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nama MK</th>
                        <td>{{ $matriksCpmk->nama_mk_matrix ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>CPMK</th>
                        <td>
                            <span class="badge bg-info">{{ $matriksCpmk->cpmk->kode_cpmk }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>CPL Induk</th>
                        <td>
                            <span class="badge bg-primary">{{ $matriksCpmk->cpmk->cpl->kode_cpl }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Bobot Penilaian</h6>
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Partisipatif</th>
                            <th class="text-center">Tugas</th>
                            <th class="text-center">UTS</th>
                            <th class="text-center">UAS</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>{{ $matriksCpmk->bobot_partisipatif }}</td>
                            <td>{{ $matriksCpmk->bobot_tugas }}</td>
                            <td>{{ $matriksCpmk->bobot_uts }}</td>
                            <td>{{ $matriksCpmk->bobot_uas }}</td>
                            <td><strong>{{ $matriksCpmk->skor_maksimal }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="alert alert-info mt-3">
                    <strong>Catatan:</strong> Total bobot ({{ $matriksCpmk->bobot_partisipatif + $matriksCpmk->bobot_tugas + $matriksCpmk->bobot_uts + $matriksCpmk->bobot_uas }})
                    {{ ($matriksCpmk->bobot_partisipatif + $matriksCpmk->bobot_tugas + $matriksCpmk->bobot_uts + $matriksCpmk->bobot_uas) == $matriksCpmk->skor_maksimal ? 'sesuai' : 'TIDAK sesuai' }}
                    dengan skor maksimal ({{ $matriksCpmk->skor_maksimal }})
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
