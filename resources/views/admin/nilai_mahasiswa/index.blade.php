@extends('admin.layouts.app')

@section('title', 'Data Nilai Mahasiswa')

@section('page-title', 'Nilai Mahasiswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <form action="{{ route('admin.nilai-mahasiswa.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="nim" class="form-control" placeholder="Filter NIM..." value="{{ $nim ?? '' }}">
                    <input type="text" name="nama_mk" class="form-control" placeholder="Filter Mata Kuliah..." value="{{ $namaMk ?? '' }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i></button>
                    @if($nim || $namaMk)
                        <a href="{{ route('admin.nilai-mahasiswa.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.nilai-mahasiswa.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Input Nilai
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
                        <th width="12%">NIM</th>
                        <th width="20%">Nama MK</th>
                        <th width="6%">Tugas</th>
                        <th width="6%">UTS</th>
                        <th width="6%">UAS</th>
                        <th width="8%">Part</th>
                        <th width="8%">Akhir</th>
                        <th width="6%">Skala 4</th>
                        <th width="6%">Huruf</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nilai as $index => $item)
                    <tr>
                        <td>{{ $nilai->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $item->nim }}</strong>
                            @if($item->mahasiswa)
                                <br><small class="text-muted">{{ $item->mahasiswa->nama }}</small>
                            @endif
                        </td>
                        <td>{{ $item->nama_mk_siakad }}</td>
                        <td class="text-center">{{ $item->tugas ?? '-' }}</td>
                        <td class="text-center">{{ $item->uts ?? '-' }}</td>
                        <td class="text-center">{{ $item->uas ?? '-' }}</td>
                        <td class="text-center">{{ $item->partisipatif ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $item->nilai_akhir }}</strong></td>
                        <td class="text-center">{{ $item->nilai_skala4 ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $item->huruf == 'A' || $item->huruf == 'B+' ? 'success' : ($item->huruf == 'D' || $item->huruf == 'E' ? 'danger' : 'warning') }}">
                                {{ $item->huruf ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.nilai-mahasiswa.show', $item->id) }}" class="btn btn-sm btn-success" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.nilai-mahasiswa.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.nilai-mahasiswa.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus nilai ini?')">
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
                        <td colspan="11" class="text-center">Belum ada data nilai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $nilai->links() }}
        </div>
    </div>
</div>
@endsection
