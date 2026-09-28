@extends('admin.layouts.app')

@section('title', 'Edit CPL')

@section('page-title', 'CPL - Edit Data')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit CPL</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.cpl.update', $cpl->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="prodi_id" class="form-label">Prodi <span class="text-danger">*</span></label>
                <select class="form-select @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id" required>
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('prodi_id', $cpl->prodi_id) == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}
                        </option>
                    @endforeach
                </select>
                @error('prodi_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kode_cpl" class="form-label">Kode CPL <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('kode_cpl') is-invalid @enderror"
                       id="kode_cpl" name="kode_cpl" value="{{ old('kode_cpl', $cpl->kode_cpl) }}"
                       placeholder="Contoh: CPL-01" required>
                @error('kode_cpl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi', $cpl->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="ambang_minimum" class="form-label">Ambang Minimum <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control @error('ambang_minimum') is-invalid @enderror"
                           id="ambang_minimum" name="ambang_minimum"
                           value="{{ old('ambang_minimum', $cpl->ambang_minimum) }}"
                           min="0" max="100" step="0.01" required>
                    <span class="input-group-text">%</span>
                </div>
                @error('ambang_minimum')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update
                </button>
                <a href="{{ route('admin.cpl.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
