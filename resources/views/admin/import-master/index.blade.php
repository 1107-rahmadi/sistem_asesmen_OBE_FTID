@extends('admin.layouts.app')

@section('title', 'Import Data Master')

@section('page-title', 'Import Data Master')

@push('styles')
<style>
    .upload-zone {
        border: 2px dashed #3498db;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s;
    }
    .upload-zone:hover {
        background: #e9ecef;
        border-color: #2980b9;
    }
    .upload-zone.dragover {
        background: #d1ecf1;
        border-color: #0cabc0;
    }
    .preview-table {
        font-size: 12px;
    }
    .preview-table th {
        background: #2c3e50;
        color: white;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .row-valid {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    .row-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .row-error {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .conflict-card {
        border-left: 4px solid #dc3545;
        background: #fff5f5;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
    }
    .summary-box {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        margin-right: 10px;
    }
</style>
@endpush

@section('content')

{{-- Alert Messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<h4 class="mb-3"><i class="fas fa-file-import me-2"></i>Import Data Master (CPL, CPMK, Matriks)</h4>

{{-- Upload Form (shown when no preview) --}}
@if(!$previewData)
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.import-master.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="upload-zone mb-4" id="uploadZone">
                <i class="fas fa-cloud-upload-alt fa-4x text-primary mb-3"></i>
                <h5>Drag & Drop file Excel di sini</h5>
                <p class="text-muted mb-3">atau</p>
                <label class="btn btn-primary">
                    <i class="fas fa-folder-open me-1"></i> Pilih File
                    <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" style="display: none;">
                </label>
                <p class="text-muted mt-3 mb-0">
                    <small>Format: .xlsx, .xls, .csv | Maksimal: 10MB</small>
                </p>
                <p class="text-muted mb-0">
                    <small><strong>Template:</strong> File CSV dengan delimiter <code>;</code> (titik koma), buka di Excel akan otomatis dalam kolom terpisah.</small>
                </p>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.import-master.template') }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Download Template
                </a>
                <button type="submit" class="btn btn-primary" id="uploadBtn" disabled>
                    <i class="fas fa-upload me-1"></i> Upload & Preview
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Preview Section (shown when data is available) --}}
@if($previewData)
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview Data</h5>
            <div>
                <span class="badge bg-light text-dark me-2">
                    <i class="fas fa-file me-1"></i> {{ $filename }}
                </span>
                <a href="{{ route('admin.import-master.clear') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-times me-1"></i> Batal / Upload Ulang
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- Summary --}}
        @php
            $totalRows = count($previewData);
            $validRows = collect($previewData)->where('is_valid', true)->count();
            $warningRows = collect($previewData)->filter(function($item) {
                return $item['is_valid'] && ($item['forward_filled'] || !empty($item['cpl_variation_issue']));
            })->count();
            $invalidRows = collect($previewData)->where('is_valid', false)->count();
        @endphp

        <div class="mb-3">
            <span class="summary-box bg-success text-white">
                <i class="fas fa-check me-1"></i> {{ $validRows }} Baris Valid
            </span>
            @if($warningRows > 0)
            <span class="summary-box bg-warning text-dark">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ $warningRows }} Perlu Review
            </span>
            @endif
            @if($invalidRows > 0)
            <span class="summary-box bg-danger text-white">
                <i class="fas fa-times me-1"></i> {{ $invalidRows }} Bermasalah
            </span>
            @endif
            <span class="summary-box bg-secondary text-white">
                Total: {{ $totalRows }} Baris
            </span>
        </div>

        {{-- Issues --}}
        @if(!empty($previewIssues))
        <div class="alert alert-warning mb-3">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Catatan Penting:</h6>
            <ul class="mb-0">
                @foreach($previewIssues as $issue)
                <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Conflicts --}}
        @if(!empty($previewConflicts))
        <div class="alert alert-danger mb-3">
            <h6><i class="fas fa-exclamation-circle me-2"></i>Konflik Nama MK (Butuh Keputusan Manual):</h6>
            <p class="mb-2">Kode MK yang sama memiliki nama berbeda di beberapa baris. Pilih nama yang benar:</p>

            <form action="{{ route('admin.import-master.process') }}" method="POST" id="conflictForm">
                @csrf

                @foreach($previewConflicts as $conflict)
                <div class="conflict-card">
                    <strong>Kode MK: {{ $conflict['kode_mk'] }} (Baris {{ $conflict['row'] }})</strong>
                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="conflict_resolutions[{{ $conflict['row'] }}_{{ $conflict['kode_mk'] }}]"
                                   id="conflict_{{$conflict['row']}}_1"
                                   value="{{ $conflict['nama_1'] }}" required>
                            <label class="form-check-label" for="conflict_{{$conflict['row']}}_1">
                                "{{ $conflict['nama_1'] }}"
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="conflict_resolutions[{{ $conflict['row'] }}_{{ $conflict['kode_mk'] }}]"
                                   id="conflict_{{$conflict['row']}}_2"
                                   value="{{ $conflict['nama_2'] }}">
                            <label class="form-check-label" for="conflict_{{$conflict['row']}}_2">
                                "{{ $conflict['nama_2'] }}"
                            </label>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="skipConflicts" name="skip_conflicts" value="1">
                    <label class="form-check-label" for="skipConflicts">
                        <strong>Lewati konflik</strong> - Gunakan nama pertama yang ditemukan untuk setiap kode MK
                    </label>
                </div>
            </form>
        </div>
        @endif

        {{-- Preview Table --}}
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered preview-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode CPL</th>
                        <th>Deskripsi CPL</th>
                        <th>Kode MK</th>
                        <th>Nama MK</th>
                        <th>Kode CPMK</th>
                        <th>Deskripsi CPMK</th>
                        <th>Part</th>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Skor Max</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewData as $row)
                    @php
                        $rowClass = 'row-valid';
                        $statusBadge = '<span class="badge bg-success">Valid</span>';

                        if (!$row['is_valid']) {
                            $rowClass = 'row-error';
                            $statusBadge = '<span class="badge bg-danger">Data Tidak Lengkap</span>';
                        } elseif ($row['forward_filled'] || !empty($row['cpl_variation_issue'])) {
                            $rowClass = 'row-warning';
                            $statusBadge = '<span class="badge bg-warning">Forward-fill</span>';
                        }
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $row['row_num'] }}</td>
                        <td><strong>{{ $row['kode_cpl'] ?? '-' }}</strong></td>
                        <td>{{ Str::limit($row['deskripsi_cpl'] ?? '-', 50) }}</td>
                        <td>{{ $row['kode_mk'] ?? '-' }}</td>
                        <td>{{ $row['nama_mk'] ?? '-' }}</td>
                        <td><strong>{{ $row['kode_cpmk'] ?? '-' }}</strong></td>
                        <td>{{ Str::limit($row['deskripsi_cpmk'] ?? '-', 40) }}</td>
                        <td class="text-center">{{ $row['bobot_partisipatif'] ?? '-' }}</td>
                        <td class="text-center">{{ $row['bobot_tugas'] ?? '-' }}</td>
                        <td class="text-center">{{ $row['bobot_uts'] ?? '-' }}</td>
                        <td class="text-center">{{ $row['bobot_uas'] ?? '-' }}</td>
                        <td class="text-center">{{ $row['skor_maksimal'] ?? '-' }}</td>
                        <td>{!! $statusBadge !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('admin.import-master.clear') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Batal
            </a>

            @if(empty($previewConflicts))
            <form action="{{ route('admin.import-master.process') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Proses dan simpan semua data ke database?')">
                    <i class="fas fa-save me-1"></i> Proses & Simpan
                </button>
            </form>
            @else
            <button type="submit" form="conflictForm" class="btn btn-success btn-lg" onclick="return confirm('Proses dan simpan semua data ke database?')">
                <i class="fas fa-save me-1"></i> Proses & Simpan (dengan resolusi konflik)
            </button>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Instructions --}}
<div class="card">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Petunjuk</h5>
    </div>
    <div class="card-body">
        <ol class="mb-0">
            <li class="mb-2">Klik <strong>"Download Template"</strong> untuk mendapatkan format file CSV yang bisa dibuka di Excel.</li>
            <li class="mb-2">Isi file sesuai format template. Kolom <strong>Kode CPL, Kode MK, Kode CPMK, Skor Maksimal</strong> wajib diisi.</li>
            <li class="mb-2">Kolom <strong>Kode CPL, CPL, Kode, Mata Kuliah</strong> boleh kosong di baris berikutnya (akan auto-fill dari baris di atasnya).</li>
            <li class="mb-2">Upload file dan <strong>review preview</strong> sebelum data disimpan ke database.</li>
            <li class="mb-2"><strong>Warna hijau</strong> = valid, <strong>Kuning</strong> = forward-fill diterapkan, <strong>Merah</strong> = perlu perhatian.</li>
            <li>Data yang sudah ada akan di-<strong>update</strong>, data baru akan di-<strong>insert</strong>.</li>
        </ol>
    </div>
</div>

@endsection

@push('scripts')
<script>
// File input handling
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const uploadBtn = document.getElementById('uploadBtn');

// Drag and drop
uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');

    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        uploadBtn.disabled = false;
        uploadZone.querySelector('h5').textContent = e.dataTransfer.files[0].name;
    }
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length) {
        uploadBtn.disabled = false;
        uploadZone.querySelector('h5').textContent = fileInput.files[0].name;
    }
});
</script>
@endpush
