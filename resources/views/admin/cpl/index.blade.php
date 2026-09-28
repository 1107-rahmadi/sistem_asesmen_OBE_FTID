@extends('admin.layouts.app')

@section('title', 'Data CPL')

@section('page-title', 'CPL - Capaian Pembelajaran Lulusan')

@push('styles')
<style>
    .import-section {
        background: #f8f9fa;
        border: 2px dashed #3498db;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .import-section.collapsed {
        display: none;
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

{{-- Alert --}}
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
        <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Import CPL dari Excel</h5>
        <button class="btn btn-sm btn-secondary" type="button" onclick="toggleImport()">
            <i class="fas fa-chevron-up" id="importToggleIcon"></i>
        </button>
    </div>

    {{-- Form Upload --}}
    <form action="{{ route('admin.cpl.upload-import') }}" method="POST" enctype="multipart/form-data" id="importForm">
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
                    <a href="{{ route('admin.cpl.template') }}" class="btn btn-outline-success">
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
    $newCount = count(array_filter($importData, fn($item) => $item['is_new']));
    $updateCount = count(array_filter($importData, fn($item) => $item['is_update']));
    $skipCount = count(array_filter($importData, fn($item) => !$item['is_valid']));
@endphp

<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Import CPL</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <span class="badge bg-success me-2">{{ $newCount }} CPL Baru</span>
            <span class="badge bg-warning text-dark me-2">{{ $updateCount }} Akan Diupdate</span>
            @if($skipCount > 0)
                <span class="badge bg-danger me-2">{{ $skipCount }} Dilewati</span>
            @endif
        </div>

        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            <table class="table table-bordered preview-table">
                <thead class="table-dark">
                    <tr>
                        <th>Baris</th>
                        <th>Kode CPL</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Deskripsi Lama (jika update)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($importData as $row)
                    <tr class="{{ $row['is_new'] ? 'row-new' : ($row['is_update'] ? 'row-update' : 'row-skip') }}">
                        <td>{{ $row['row_num'] }}</td>
                        <td><strong>{{ $row['kode_cpl'] }}</strong></td>
                        <td>{{ Str::limit($row['deskripsi'], 80) }}</td>
                        <td>
                            @if($row['is_new'])
                                <span class="badge bg-success">BARU</span>
                            @elseif($row['is_update'])
                                <span class="badge bg-warning text-dark">UPDATE</span>
                            @else
                                <span class="badge bg-danger">DILEWATI</span>
                            @endif
                        </td>
                        <td>
                            @if($row['is_update'] && $row['deskripsi_lama'])
                                <small class="text-muted">{{ Str::limit($row['deskripsi_lama'], 60) }}</small>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('admin.cpl.cancel-import') }}" class="btn btn-secondary">
                Batal
            </a>
            <form action="{{ route('admin.cpl.process-import') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('Proses import {{ count($importData) }} baris?')">
                    <i class="fas fa-save me-1"></i> Proses & Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Listing CPL --}}
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('admin.cpl.index') }}" method="GET" class="d-flex gap-2">
                    <select name="prodi_id" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
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
                        <a href="{{ route('admin.cpl.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.cpl.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah CPL
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="8%">Kode CPL</th>
                        <th>Deskripsi</th>
                        <th width="8%">Prodi</th>
                        <th width="8%">Ambang Min</th>
                        <th width="10%">CPMK</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cpls as $index => $cpl)
                    <tr>
                        <td>{{ $cpls->firstItem() + $index }}</td>
                        <td><strong>{{ $cpl->kode_cpl }}</strong></td>
                        <td>{{ Str::limit($cpl->deskripsi, 80) }}</td>
                        <td>
                            @if($cpl->prodi)
                                <span class="badge bg-primary">{{ $cpl->prodi->kode_prodi }}</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $cpl->ambang_minimum }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $cpl->cpmk->count() }} CPMK</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.cpl.show', $cpl->id) }}" class="btn btn-sm btn-success" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.cpl.edit', $cpl->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.cpl.destroy', $cpl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" {{ $cpl->cpmk->count() > 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data CPL</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $cpls->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleImport() {
    const section = document.getElementById('importSection');
    const icon = document.getElementById('importToggleIcon');
    section.classList.toggle('import-section');
    if (section.classList.contains('import-section')) {
        section.classList.remove('collapsed');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        section.classList.add('collapsed');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endpush
