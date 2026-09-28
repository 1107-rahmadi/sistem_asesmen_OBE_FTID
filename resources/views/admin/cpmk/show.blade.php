@extends('admin.layouts.app')

@section('title', 'Detail CPMK')

@section('page-title', 'CPMK - Detail')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detail CPMK</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.cpmk.index') }}" class="btn btn-secondary btn-sm">
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
                        <th width="30%">Kode CPMK</th>
                        <td><strong>{{ $cpmk->kode_cpmk }}</strong></td>
                    </tr>
                    <tr>
                        <th>CPL Induk</th>
                        <td>
                            <span class="badge bg-primary">{{ $cpmk->cpl->kode_cpl }}</span>
                            <br>
                            <small>{{ Str::limit($cpmk->cpl->deskripsi, 80) }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Ambang Minimum</th>
                        <td>{{ $cpmk->ambang_minimum }}%</td>
                    </tr>
                    <tr>
                        <th>Jumlah MK</th>
                        <td><span class="badge bg-info">{{ $cpmk->matriksCpmk->count() }} Mata Kuliah</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>Deskripsi</th>
                    </tr>
                    <tr>
                        <td>{{ $cpmk->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <h6>Daftar Mata Kuliah yang Mendukung</h6>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th width="15%">Kode MK</th>
                        <th>Nama MK</th>
                        <th width="10%">Part</th>
                        <th width="10%">Tugas</th>
                        <th width="10%">UTS</th>
                        <th width="10%">UAS</th>
                        <th width="10%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cpmk->matriksCpmk as $mk)
                    <tr>
                        <td><strong>{{ $mk->kode_mk }}</strong></td>
                        <td>{{ $mk->nama_mk_matrix ?? '-' }}</td>
                        <td class="text-center">{{ $mk->bobot_partisipatif }}</td>
                        <td class="text-center">{{ $mk->bobot_tugas }}</td>
                        <td class="text-center">{{ $mk->bobot_uts }}</td>
                        <td class="text-center">{{ $mk->bobot_uas }}</td>
                        <td class="text-center"><strong>{{ $mk->skor_maksimal }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada mata kuliah</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
