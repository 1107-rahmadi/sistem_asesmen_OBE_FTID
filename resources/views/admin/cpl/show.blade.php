@extends('admin.layouts.app')

@section('title', 'Detail CPL')

@section('page-title', 'CPL - Detail')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detail CPL</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.cpl.index') }}" class="btn btn-secondary btn-sm">
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
                        <th width="30%">Kode CPL</th>
                        <td><strong>{{ $cpl->kode_cpl }}</strong></td>
                    </tr>
                    <tr>
                        <th>Ambang Minimum</th>
                        <td>{{ $cpl->ambang_minimum }}%</td>
                    </tr>
                    <tr>
                        <th>Jumlah CPMK</th>
                        <td><span class="badge bg-info">{{ $cpl->cpmk->count() }} CPMK</span></td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $cpl->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th>Deskripsi</th>
                    </tr>
                    <tr>
                        <td>{{ $cpl->deskripsi }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <h6>Daftar CPMK</h6>
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th width="15%">Kode CPMK</th>
                        <th>Deskripsi</th>
                        <th width="15%">Ambang Min</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cpl->cpmk as $cpmk)
                    <tr>
                        <td><strong>{{ $cpmk->kode_cpmk }}</strong></td>
                        <td>{{ $cpmk->deskripsi ?? '-' }}</td>
                        <td class="text-center">{{ $cpmk->ambang_minimum }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Belum ada CPMK</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
