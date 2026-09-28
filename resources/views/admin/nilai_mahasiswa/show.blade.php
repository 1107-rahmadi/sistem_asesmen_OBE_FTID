@extends('admin.layouts.app')

@section('title', 'Detail Nilai')

@section('page-title', 'Nilai Mahasiswa - Detail')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Detail Nilai</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.nilai-mahasiswa.index') }}" class="btn btn-secondary btn-sm">
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
                        <th width="35%">NIM</th>
                        <td><strong>{{ $nilai->nim }}</strong></td>
                    </tr>
                    <tr>
                        <th>Mahasiswa</th>
                        <td>{{ $nilai->mahasiswa->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Mata Kuliah</th>
                        <td>{{ $nilai->nama_mk_siakad }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $nilai->created_at ? $nilai->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Ringkasan Nilai</h6>
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Tugas</th>
                            <th class="text-center">UTS</th>
                            <th class="text-center">UAS</th>
                            <th class="text-center">Partisipatif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td>{{ $nilai->tugas ?? '-' }}</td>
                            <td>{{ $nilai->uts ?? '-' }}</td>
                            <td>{{ $nilai->uas ?? '-' }}</td>
                            <td>{{ $nilai->partisipatif ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Nilai Akhir</th>
                            <th class="text-center">Skala 4</th>
                            <th class="text-center">Huruf</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td><strong>{{ $nilai->nilai_akhir }}</strong></td>
                            <td><strong>{{ $nilai->nilai_skala4 ?? '-' }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $nilai->huruf == 'A' || $nilai->huruf == 'A-' || $nilai->huruf == 'B+' ? 'success' : ($nilai->huruf == 'D' || $nilai->huruf == 'E' ? 'danger' : 'warning') }} fs-6">
                                    {{ $nilai->huruf ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <hr>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.nilai-mahasiswa.edit', $nilai->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('admin.nilai-mahasiswa.destroy', $nilai->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus nilai ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
