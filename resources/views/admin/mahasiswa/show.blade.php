@extends('admin.layouts.app')

@section('title', 'Detail Mahasiswa')

@section('page-title', 'Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-user"></i> Detail Mahasiswa</h5>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th width="200">NIM</th>
                <td>: <strong>{{ $mahasiswa->nim }}</strong></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>: {{ $mahasiswa->nama }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>: {{ $mahasiswa->kelas_raw ?? '-' }}</td>
            </tr>
        </table>

        <hr>

        <h6 class="mb-3"><i class="fas fa-chart-line"></i> Riwayat Nilai ({{ $nilaiMahasiswas->count() }} matkul)</h6>
        @if($nilaiMahasiswas->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>No</th>
                            <th>Mata Kuliah</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Part</th>
                            <th>Akhir</th>
                            <th>Skala 4</th>
                            <th>Huruf</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nilaiMahasiswas as $index => $nilai)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $nilai->nama_mk_siakad }}</td>
                            <td class="text-center">{{ $nilai->tugas ?? '-' }}</td>
                            <td class="text-center">{{ $nilai->uts ?? '-' }}</td>
                            <td class="text-center">{{ $nilai->uas ?? '-' }}</td>
                            <td class="text-center">{{ $nilai->partisipatif ?? '-' }}</td>
                            <td class="text-center"><strong>{{ $nilai->nilai_akhir }}</strong></td>
                            <td class="text-center">{{ $nilai->nilai_skala4 ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $nilai->huruf == 'A' || $nilai->huruf == 'B+' ? 'success' : ($nilai->huruf == 'D' || $nilai->huruf == 'E' ? 'danger' : 'warning') }}">
                                    {{ $nilai->huruf ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-secondary mb-0">
                <i class="fas fa-info-circle"></i> Belum ada data nilai untuk mahasiswa ini.
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
