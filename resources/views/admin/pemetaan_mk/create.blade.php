@extends('admin.layouts.app')

@section('title', 'Tambah Pemetaan MK')

@section('page-title', 'Pemetaan MK')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Pemetaan MK</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pemetaan-mk.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_mk_siakad" class="form-label">Nama MK (Siakad) <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_mk_siakad') is-invalid @enderror"
                    id="nama_mk_siakad" name="nama_mk_siakad" value="{{ old('nama_mk_siakad') }}" required>
                @error('nama_mk_siakad')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Nama mata kuliah sesuai di Siakad/IRS</small>
            </div>

            <div class="mb-3">
                <label for="kode_mk" class="form-label">Kode MK</label>
                <input type="text" class="form-control @error('kode_mk') is-invalid @enderror"
                    id="kode_mk" name="kode_mk" value="{{ old('kode_mk') }}">
                @error('kode_mk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror"
                    id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="status_terverifikasi" name="status_terverifikasi" value="1" {{ old('status_terverifikasi') ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_terverifikasi">
                        Terverifikasi
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('admin.pemetaan-mk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
