@extends('admin.layouts.app')

@section('title', 'Data Mahasiswa')

@section('page-title', 'Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('admin.mahasiswa.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="nim" class="form-control" placeholder="Filter NIM..." value="{{ $nim ?? '' }}">
                    <input type="text" name="nama" class="form-control" placeholder="Filter Nama..." value="{{ $nama ?? '' }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                    @if($nim || $nama)
                        <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Mahasiswa
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
                        <th width="15%">NIM</th>
                        <th>Nama</th>
                        <th width="15%">Kelas</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $index => $item)
                    <tr>
                        <td>{{ $mahasiswas->firstItem() + $index }}</td>
                        <td><strong>{{ $item->nim }}</strong></td>
                        <td>{{ $item->nama }}</td>
                        <td>{{ $item->kelas_raw ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.mahasiswa.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.mahasiswa.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.mahasiswa.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus mahasiswa ini?')">
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
                        <td colspan="5" class="text-center">Belum ada data mahasiswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $mahasiswas->links() }}
        </div>
    </div>
</div>
@endsection
