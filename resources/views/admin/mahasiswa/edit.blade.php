@extends('admin.layouts.app')

@section('title', 'Edit Mahasiswa')

@section('page-title', 'Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Mahasiswa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nim') is-invalid @enderror"
                    id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required>
                @error('nim')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                    id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kelas_raw" class="form-label">Kelas</label>
                <input type="text" class="form-control @error('kelas_raw') is-invalid @enderror"
                    id="kelas_raw" name="kelas_raw" value="{{ old('kelas_raw', $mahasiswa->kelas_raw) }}">
                @error('kelas_raw')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
