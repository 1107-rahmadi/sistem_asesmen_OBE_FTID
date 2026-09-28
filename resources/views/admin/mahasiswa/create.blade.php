@extends('admin.layouts.app')

@section('title', 'Tambah Mahasiswa')

@section('page-title', 'Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Mahasiswa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.mahasiswa.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nim') is-invalid @enderror"
                    id="nim" name="nim" value="{{ old('nim') }}" required>
                @error('nim')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                    id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kelas_raw" class="form-label">Kelas</label>
                <input type="text" class="form-control @error('kelas_raw') is-invalid @enderror"
                    id="kelas_raw" name="kelas_raw" value="{{ old('kelas_raw') }}">
                @error('kelas_raw')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Contoh: SI-3A, SI-3B</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
