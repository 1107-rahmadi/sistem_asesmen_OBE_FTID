@extends('admin.layouts.app')

@section('title', 'Data Pemetaan MK')

@section('page-title', 'Pemetaan MK')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('admin.pemetaan-mk.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select" style="max-width: 180px;">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ $filterStatus === '1' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="0" {{ $filterStatus === '0' ? 'selected' : '' }}>Belum Terverifikasi</option>
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/kode MK..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                    @if($filterStatus !== '' || $search)
                        <a href="{{ route('admin.pemetaan-mk.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.pemetaan-mk.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pemetaan MK
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Kode MK</th>
                        <th>Nama MK (Siakad)</th>
                        <th width="15%">Status</th>
                        <th width="20%">Catatan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemetaan as $index => $item)
                    <tr>
                        <td>{{ $pemetaan->firstItem() + $index }}</td>
                        <td><strong>{{ $item->kode_mk ?? '-' }}</strong></td>
                        <td>{{ $item->nama_mk_siakad }}</td>
                        <td class="text-center">
                            @if($item->status_terverifikasi)
                                <span class="badge bg-success">Terverifikasi</span>
                            @else
                                <span class="badge bg-warning">Belum</span>
                            @endif
                        </td>
                        <td>{{ $item->catatan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.pemetaan-mk.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pemetaan-mk.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.pemetaan-mk.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
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
                        <td colspan="6" class="text-center">Belum ada data Pemetaan MK</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $pemetaan->links() }}
        </div>
    </div>
</div>
@endsection
