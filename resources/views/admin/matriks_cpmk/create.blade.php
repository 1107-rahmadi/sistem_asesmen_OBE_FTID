@extends('admin.layouts.app')

@section('title', 'Tambah Matriks CPMK')

@section('page-title', 'Matriks CPMK - Tambah Data')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus me-2"></i> Tambah Matriks CPMK</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.matriks-cpmk.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cpmk_id" class="form-label">CPMK <span class="text-danger">*</span></label>
                        <select class="form-select @error('cpmk_id') is-invalid @enderror" id="cpmk_id" name="cpmk_id" required>
                            <option value="">-- Pilih CPMK --</option>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}" {{ old('cpmk_id') == $cpmk->id ? 'selected' : '' }}>
                                    {{ $cpmk->kode_cpmk }} ({{ $cpmk->cpl->kode_cpl }})
                                </option>
                            @endforeach
                        </select>
                        @error('cpmk_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode_mk" class="form-label">Kode MK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_mk') is-invalid @enderror"
                               id="kode_mk" name="kode_mk" value="{{ old('kode_mk') }}"
                               placeholder="Contoh: MK-01" required>
                        @error('kode_mk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="nama_mk_matrix" class="form-label">Nama MK (sesuai Matrix)</label>
                <input type="text" class="form-control @error('nama_mk_matrix') is-invalid @enderror"
                       id="nama_mk_matrix" name="nama_mk_matrix" value="{{ old('nama_mk_matrix') }}"
                       placeholder="Nama mata kuliah sesuai matrix kurikulum">
                @error('nama_mk_matrix')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h6>Bobot Penilaian</h6>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_partisipatif" class="form-label">Partisipatif</label>
                        <input type="number" class="form-control" name="bobot_partisipatif"
                               value="{{ old('bobot_partisipatif', 0) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_tugas" class="form-label">Tugas</label>
                        <input type="number" class="form-control" name="bobot_tugas"
                               value="{{ old('bobot_tugas', 0) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_uts" class="form-label">UTS</label>
                        <input type="number" class="form-control" name="bobot_uts"
                               value="{{ old('bobot_uts', 0) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_uas" class="form-label">UAS</label>
                        <input type="number" class="form-control" name="bobot_uas"
                               value="{{ old('bobot_uas', 0) }}" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="skor_maksimal" class="form-label">Skor Maksimal <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('skor_maksimal') is-invalid @enderror"
                       id="skor_maksimal" name="skor_maksimal" value="{{ old('skor_maksimal', 100) }}"
                       min="0" step="0.01" required>
                @error('skor_maksimal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Total poin maksimal untuk pembagi perhitungan capaian</small>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('admin.matriks-cpmk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
