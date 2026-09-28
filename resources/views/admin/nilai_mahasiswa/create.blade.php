@extends('admin.layouts.app')

@section('title', 'Input Nilai')

@section('page-title', 'Nilai Mahasiswa - Input Baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i> Input Nilai Mahasiswa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.nilai-mahasiswa.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nim') is-invalid @enderror"
                               id="nim" name="nim" value="{{ old('nim') }}"
                               placeholder="Contoh: 210202001" required>
                        @error('nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">NIM mahasiswa (akan auto-create jika belum ada)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_mk_siakad" class="form-label">Nama Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_mk_siakad') is-invalid @enderror"
                               id="nama_mk_siakad" name="nama_mk_siakad"
                               value="{{ old('nama_mk_siakad') }}"
                               list="daftar-mk"
                               placeholder="Ketik atau pilih MK..." required>
                        <datalist id="daftar-mk">
                            @foreach($daftarMk as $mk)
                                <option value="{{ $mk }}">
                            @endforeach
                        </datalist>
                        @error('nama_mk_siakad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nama MK sesuai data SEVIMA/SIAKAD</small>
                    </div>
                </div>
            </div>

            <hr>
            <h6>Komponen Nilai</h6>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tugas" class="form-label">Tugas</label>
                        <input type="number" class="form-control" name="tugas"
                               value="{{ old('tugas') }}" min="0" max="100" step="0.01"
                               placeholder="0-100">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="uts" class="form-label">UTS</label>
                        <input type="number" class="form-control" name="uts"
                               value="{{ old('uts') }}" min="0" max="100" step="0.01"
                               placeholder="0-100">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="uas" class="form-label">UAS</label>
                        <input type="number" class="form-control" name="uas"
                               value="{{ old('uas') }}" min="0" max="100" step="0.01"
                               placeholder="0-100">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="partisipatif" class="form-label">Partisipatif</label>
                        <input type="number" class="form-control" name="partisipatif"
                               value="{{ old('partisipatif') }}" min="0" max="100" step="0.01"
                               placeholder="0-100">
                    </div>
                </div>
            </div>

            <hr>
            <h6>Nilai Akhir</h6>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nilai_akhir" class="form-label">Nilai Akhir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('nilai_akhir') is-invalid @enderror"
                               id="nilai_akhir" name="nilai_akhir"
                               value="{{ old('nilai_akhir') }}" min="0" max="100" step="0.01" required>
                        @error('nilai_akhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nilai akhir dari SEVIMA</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="nilai_skala4" class="form-label">Nilai Skala 4</label>
                        <input type="number" class="form-control" name="nilai_skala4"
                               value="{{ old('nilai_skala4') }}" min="0" max="4" step="0.01"
                               placeholder="Auto (kosongkan)">
                        <small class="text-muted">Auto-konversi jika kosong</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="huruf" class="form-label">Nilai Huruf</label>
                        <input type="text" class="form-control" name="huruf"
                               value="{{ old('huruf') }}" maxlength="2"
                               placeholder="Auto (kosongkan)">
                        <small class="text-muted">Auto-konversi jika kosong</small>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('admin.nilai-mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
