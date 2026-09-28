@extends('admin.layouts.app')

@section('title', 'Data Matriks CPMK')

@section('page-title', 'Matriks CPMK')

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
        <h5 class="mb-0"><i class="fas fa-file-import me-2"></i>Import Matriks CPMK dari Excel</h5>
        <button class="btn btn-sm btn-secondary" onclick="toggleImport()">
            <i class="fas fa-chevron-up" id="importToggleIcon"></i>
        </button>
    </div>

    <form action="{{ route('admin.matriks-cpmk.upload-import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-8">
                <p class="text-muted small mb-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Kolom: CPL (info), Kode MK, Nama MK, Kode CPMK, Deskripsi CPMK, Skor, Part, Tugas, UTS, UAS.
                    CPMK harus sudah diimpor duluan.
                </p>
                <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload & Preview
                    </button>
                    <a href="{{ route('admin.matriks-cpmk.template') }}" class="btn btn-outline-success">
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
@endphp

<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Import Matriks CPMK</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
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
                        <th>CPL</th>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th>Kode CPMK</th>
                        <th>Part</th>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Skor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($importData as $row)
                    <tr class="{{ ($row['is_new'] ?? false) ? 'row-new' : ((($row['is_update'] ?? false) ? 'row-update' : 'row-skip')) }}">
                        <td>{{ $row['row_num'] }}</td>
                        <td><strong>{{ $row['kode_cpl'] ?: '-' }}</strong></td>
                        <td>{{ $row['kode_mk'] }}</td>
                        <td>{{ Str::limit($row['nama_mk'] ?? '-', 30) }}</td>
                        <td>{{ $row['kode_cpmk'] }}</td>
                        <td class="text-center">{{ $row['bobot_part'] }}</td>
                        <td class="text-center">{{ $row['bobot_tugas'] }}</td>
                        <td class="text-center">{{ $row['bobot_uts'] }}</td>
                        <td class="text-center">{{ $row['bobot_uas'] }}</td>
                        <td class="text-center"><strong>{{ $row['skor'] }}</strong></td>
                        <td>
                            @if($row['is_new'] ?? false)
                                <span class="badge bg-success">BARU</span>
                            @elseif($row['is_update'] ?? false)
                                <span class="badge bg-warning text-dark">UPDATE</span>
                            @else
                                <span class="badge bg-danger">DILEWATI</span>
                                <br><small class="text-danger">CPMK belum ada</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('admin.matriks-cpmk.cancel-import') }}" class="btn btn-secondary">Batal</a>
            <form action="{{ route('admin.matriks-cpmk.process-import') }}" method="POST" class="d-inline">
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
                <form action="{{ route('admin.matriks-cpmk.index') }}" method="GET" class="d-flex gap-2">
                    <select name="cpmk_id" class="form-select" style="max-width: 200px;">
                        <option value="">-- Filter CPMK --</option>
                        @foreach($cpmks as $cpmk)
                            <option value="{{ $cpmk->id }}" {{ $filterCpmk == $cpmk->id ? 'selected' : '' }}>
                                {{ $cpmk->kode_cpmk }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Kode/Nama MK..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i></button>
                    @if($filterCpmk || $search)
                        <a href="{{ route('admin.matriks-cpmk.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.matriks-cpmk.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Matriks
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-dark">
                    <tr>
                        <th width="4%">No</th>
                        <th width="10%">Kode MK</th>
                        <th>Nama MK</th>
                        <th width="10%">CPMK</th>
                        <th width="10%">Part</th>
                        <th width="10%">Tugas</th>
                        <th width="10%">UTS</th>
                        <th width="10%">UAS</th>
                        <th width="8%">Total</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matriks as $index => $item)
                    <tr>
                        <td>{{ $matriks->firstItem() + $index }}</td>
                        <td><strong>{{ $item->kode_mk }}</strong></td>
                        <td>{{ $item->nama_mk_matrix ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $item->cpmk->kode_cpmk }}</span>
                            <br>
                            <small class="text-muted">{{ $item->cpmk->cpl->kode_cpl }}</small>
                        </td>
                        <td class="text-center">{{ $item->bobot_partisipatif }}</td>
                        <td class="text-center">{{ $item->bobot_tugas }}</td>
                        <td class="text-center">{{ $item->bobot_uts }}</td>
                        <td class="text-center">{{ $item->bobot_uas }}</td>
                        <td class="text-center"><strong>{{ $item->skor_maksimal }}</strong></td>
                        <td>
                            <a href="{{ route('admin.matriks-cpmk.show', $item->id) }}" class="btn btn-sm btn-success" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.matriks-cpmk.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.matriks-cpmk.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Belum ada data Matriks CPMK</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $matriks->links() }}
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
