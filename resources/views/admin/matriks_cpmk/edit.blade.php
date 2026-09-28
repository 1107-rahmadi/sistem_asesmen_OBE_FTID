@extends('admin.layouts.app')

@section('title', 'Edit Matriks CPMK')

@section('page-title', 'Matriks CPMK - Edit Data')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Matriks CPMK</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.matriks-cpmk.update', $matriks->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cpmk_id" class="form-label">CPMK <span class="text-danger">*</span></label>
                        <select class="form-select @error('cpmk_id') is-invalid @enderror" id="cpmk_id" name="cpmk_id" required>
                            <option value="">-- Pilih CPMK --</option>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}" {{ old('cpmk_id', $matriks->cpmk_id) == $cpmk->id ? 'selected' : '' }}>
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
                               id="kode_mk" name="kode_mk" value="{{ old('kode_mk', $matriks->kode_mk) }}"
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
                       id="nama_mk_matrix" name="nama_mk_matrix" value="{{ old('nama_mk_matrix', $matriks->nama_mk_matrix) }}"
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
                               value="{{ old('bobot_partisipatif', $matriks->bobot_partisipatif) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_tugas" class="form-label">Tugas</label>
                        <input type="number" class="form-control" name="bobot_tugas"
                               value="{{ old('bobot_tugas', $matriks->bobot_tugas) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_uts" class="form-label">UTS</label>
                        <input type="number" class="form-control" name="bobot_uts"
                               value="{{ old('bobot_uts', $matriks->bobot_uts) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="bobot_uas" class="form-label">UAS</label>
                        <input type="number" class="form-control" name="bobot_uas"
                               value="{{ old('bobot_uas', $matriks->bobot_uas) }}" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="skor_maksimal" class="form-label">Skor Maksimal <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('skor_maksimal') is-invalid @enderror"
                       id="skor_maksimal" name="skor_maksimal" value="{{ old('skor_maksimal', $matriks->skor_maksimal) }}"
                       min="0" step="0.01" required>
                @error('skor_maksimal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('admin.matriks-cpmk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
