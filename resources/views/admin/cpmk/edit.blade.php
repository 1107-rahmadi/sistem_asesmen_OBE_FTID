@extends('admin.layouts.app')

@section('title', 'Edit CPMK')

@section('page-title', 'CPMK - Edit Data')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i> Edit CPMK</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.cpmk.update', $cpmk->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="cpl_id" class="form-label">CPL Induk <span class="text-danger">*</span></label>
                <select class="form-select @error('cpl_id') is-invalid @enderror" id="cpl_id" name="cpl_id" required>
                    <option value="">-- Pilih CPL --</option>
                    @foreach($cpls as $cpl)
                        <option value="{{ $cpl->id }}" {{ old('cpl_id', $cpmk->cpl_id) == $cpl->id ? 'selected' : '' }}>
                            {{ $cpl->kode_cpl }} - {{ Str::limit($cpl->deskripsi, 60) }}
                        </option>
                    @endforeach
                </select>
                @error('cpl_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kode_cpmk" class="form-label">Kode CPMK <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('kode_cpmk') is-invalid @enderror"
                       id="kode_cpmk" name="kode_cpmk" value="{{ old('kode_cpmk', $cpmk->kode_cpmk) }}"
                       placeholder="Contoh: CPMK.01.1" required>
                @error('kode_cpmk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi', $cpmk->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="ambang_minimum" class="form-label">Ambang Minimum <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control @error('ambang_minimum') is-invalid @enderror"
                           id="ambang_minimum" name="ambang_minimum"
                           value="{{ old('ambang_minimum', $cpmk->ambang_minimum) }}"
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
                <a href="{{ route('admin.cpmk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
