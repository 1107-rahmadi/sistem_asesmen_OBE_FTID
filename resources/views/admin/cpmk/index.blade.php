@extends('admin.layouts.app')

@section('title', 'Data CPMK')

@section('page-title', 'CPMK - Capaian Pembelajaran MK')

@push('styles')
<style>
.import-section {
    background: #f8f9fa;
    border: 2px dashed #3498db;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}
.preview-table {
    font-size: 12px;
}
.preview-table th {
    background: #2c3e50;
    color: white;
}
.row-new {
    background-color: rgba(40, 167, 69, 0.15) !important;
}
.row-update {
    background-color: rgba(255, 193, 7, 0.15) !important;
}
.row-skip {
    background-color: rgba(220, 53, 69, 0.15) !important;
}
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Import Section --}}
<div class="import-section" id="importSection">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Import CPMK dari Excel</h5>
        <button class="btn btn-sm btn-secondary" onclick="toggleImport()">
            <i class="fas fa-chevron-up" id="importToggleIcon"></i>
        </button>
    </div>

    <form action="{{ route('admin.cpmk.upload-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Pilih Prodi <span class="text-danger">*</span></label>
                <select name="prodi_id" class="form-select" required>
                    <option value="">-- Pilih Prodi --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}">{{ $prodi->kode_prodi }} - {{ $prodi->nama_prodi }}</option>
                    @endforeach
                </select>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Dipakai untuk lookup CPL. Pastikan CPL sudah diimpor duluan.
                </small>
            </div>
            <div class="col-md-5">
                <label class="form-label">File Excel/CSV</label>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload & Preview
                    </button>
                    <a href="{{ route('admin.cpmk.template') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Template
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Import Preview --}}
@if(isset($importData) && !empty($importData))
@php
    $newCount = count(array_filter($importData, fn($item) => $item['is_new'] ?? false));
    $updateCount = count(array_filter($importData, fn($item) => $item['is_update'] ?? false));
    $skipCount = count(array_filter($importData, fn($item) => $item['is_skip'] ?? false));
    $prodiName = $prodis->firstWhere('id', $importProdiId)->nama_prodi ?? 'Unknown';
@endphp

<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Import CPMK</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>Prodi:</strong> {{ $prodiName }} &nbsp;|&nbsp;
            <span class="badge bg-success me-2">{{ $newCount }} Baru</span>
            <span class="badge bg-warning text-dark me-2">{{ $updateCount }} Update</span>
            @if($skipCount > 0)
                <span class="badge bg-danger me-2">{{ $skipCount }} Dilewati</span>
            @endif
        </div>

        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered preview-table">
                <thead class="table-dark">
                    <tr>
                        <th>Baris</th>
                        <th>CPL (Forward-fill)</th>
                        <th>Kode CPMK</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($importData as $row)
                    <tr class="{{ ($row['is_new'] ?? false) ? 'row-new' : ((($row['is_update'] ?? false) ? 'row-update' : 'row-skip')) }}">
                        <td>{{ $row['row_num'] }}</td>
                        <td><strong>{{ $row['kode_cpl'] ?? '-' }}</strong></td>
                        <td>{{ $row['kode_cpmk'] }}</td>
                        <td>{{ Str::limit($row['deskripsi'] ?? '-', 60) }}</td>
                        <td>
                            @if($row['is_new'] ?? false)
                                <span class="badge bg-success">BARU</span>
                            @elseif($row['is_update'] ?? false)
                                <span class="badge bg-warning text-dark">UPDATE</span>
                            @else
                                <span class="badge bg-danger">DILEWATI</span>
                                <br><small class="text-danger">CPL "{{ $row['kode_cpl'] ?? '' }}" belum ada, import CPL dulu</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('admin.cpmk.cancel-import') }}" class="btn btn-secondary">Batal</a>
            <form action="{{ route('admin.cpmk.process-import') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Proses import {{ count($importData) }} baris?');">
                    <i class="fas fa-save me-1"></i> Proses & Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Listing --}}
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('admin.cpmk.index') }}" method="GET" class="d-flex gap-2">
                    <select name="prodi_id" class="form-select" style="width: 140px;" onchange="this.form.submit()">
                        <option value="">-- Semua Prodi --</option>
                        @foreach($prodis as $prodi)
                            <option value="{{ $prodi->id }}" {{ ($filterProdi ?? '') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->kode_prodi }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                    @if($search || $filterProdi)
                        <a href="{{ route('admin.cpmk.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.cpmk.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah CPMK
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode CPMK</th>
                        <th>CPL Induk</th>
                        <th>Deskripsi</th>
                        <th>Ambang</th>
                        <th>MK</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cpmks as $index => $cpmk)
                    <tr>
                        <td>{{ $cpmks->firstItem() + $index }}</td>
                        <td><strong>{{ $cpmk->kode_cpmk }}</strong></td>
                        <td>
                            <span class="badge bg-primary">{{ $cpmk->cpl->kode_cpl ?? '-' }}</span>
                        </td>
                        <td>{{ Str::limit($cpmk->deskripsi ?? '-', 80) }}</td>
                        <td class="text-center">{{ $cpmk->ambang_minimum }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $cpmk->matriksCpmk->count() }} MK</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.cpmk.show', $cpmk->id) }}" class="btn btn-sm btn-success" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.cpmk.edit', $cpmk->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.cpmk.destroy', $cpmk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" {{ $cpmk->matriksCpmk->count() > 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data CPMK</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $cpmks->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleImport() {
    const section = document.getElementById('importSection');
    const icon = document.getElementById('importToggleIcon');
    section.classList.toggle('d-none');
    if (section.classList.contains('d-none')) {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    } else {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
}
</script>
@endpush
