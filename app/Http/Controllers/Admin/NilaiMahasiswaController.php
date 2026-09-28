<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MatriksCpmk;
use App\Models\NilaiMahasiswa;
use App\Models\PemetaanMk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NilaiMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NilaiMahasiswa::with('mahasiswa');

        // Filter by NIM
        if ($request->has('nim') && $request->nim) {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }

        // Filter by Nama MK
        if ($request->has('nama_mk') && $request->nama_mk) {
            $query->where('nama_mk_siakad', 'like', '%' . $request->nama_mk . '%');
        }

        $data['nilai'] = $query->orderBy('created_at', 'desc')->paginate(20);
        $data['nim'] = $request->nim;
        $data['namaMk'] = $request->nama_mk;

        return view('admin.nilai_mahasiswa.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['mahasiswas'] = Mahasiswa::orderBy('nama')->get();

        // Ambil daftar nama MK unik dari matriks_cpmk
        $data['daftarMk'] = MatriksCpmk::select('nama_mk_matrix')
            ->whereNotNull('nama_mk_matrix')
            ->groupBy('nama_mk_matrix')
            ->orderBy('nama_mk_matrix')
            ->pluck('nama_mk_matrix');

        return view('admin.nilai_mahasiswa.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'nama_mk_siakad' => 'required|string|max:150',
            'tugas' => 'nullable|numeric|min:0|max:100',
            'uts' => 'nullable|numeric|min:0|max:100',
            'uas' => 'nullable|numeric|min:0|max:100',
            'partisipatif' => 'nullable|numeric|min:0|max:100',
            'nilai_akhir' => 'required|numeric|min:0|max:100',
            'nilai_skala4' => 'nullable|numeric|min:0|max:4',
            'huruf' => 'nullable|string|max:2',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nama_mk_siakad.required' => 'Nama MK wajib diisi',
            'nilai_akhir.required' => 'Nilai akhir wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Auto create mahasiswa jika belum ada
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => $request->nim],
            ['nama' => $request->nim] // default nama = NIM
        );

        // Auto create/update pemetaan MK jika belum ada
        $pemetaan = PemetaanMk::firstOrCreate(
            ['nama_mk_siakad' => $request->nama_mk_siakad],
            ['status_terverifikasi' => false]
        );

        // Auto calculate nilai_skala4 dan huruf jika kosong
        $nilaiAkhir = $request->nilai_akhir;
        $nilaiSkala4 = $request->nilai_skala4;
        $huruf = $request->huruf;

        if (!$nilaiSkala4) {
            $nilaiSkala4 = $this->konversiNilaiSkala4($nilaiAkhir);
        }
        if (!$huruf) {
            $huruf = $this->konversiNilaiHuruf($nilaiAkhir);
        }

        NilaiMahasiswa::create([
            'nim' => $request->nim,
            'nama_mk_siakad' => $request->nama_mk_siakad,
            'tugas' => $request->tugas,
            'uts' => $request->uts,
            'uas' => $request->uas,
            'partisipatif' => $request->partisipatif,
            'nilai_akhir' => $nilaiAkhir,
            'nilai_skala4' => $nilaiSkala4,
            'huruf' => $huruf,
        ]);

        return redirect()->route('admin.nilai-mahasiswa.index')
            ->with('success', 'Nilai berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nilai = NilaiMahasiswa::with('mahasiswa')->findOrFail($id);
        return view('admin.nilai_mahasiswa.show', compact('nilai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $nilai = NilaiMahasiswa::findOrFail($id);
        $data['mahasiswas'] = Mahasiswa::orderBy('nama')->get();
        $data['nilai'] = $nilai;

        // Ambil daftar nama MK unik dari matriks_cpmk
        $data['daftarMk'] = MatriksCpmk::select('nama_mk_matrix')
            ->whereNotNull('nama_mk_matrix')
            ->groupBy('nama_mk_matrix')
            ->orderBy('nama_mk_matrix')
            ->pluck('nama_mk_matrix');

        return view('admin.nilai_mahasiswa.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $nilai = NilaiMahasiswa::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nim' => 'required|string|max:20',
            'nama_mk_siakad' => 'required|string|max:150',
            'tugas' => 'nullable|numeric|min:0|max:100',
            'uts' => 'nullable|numeric|min:0|max:100',
            'uas' => 'nullable|numeric|min:0|max:100',
            'partisipatif' => 'nullable|numeric|min:0|max:100',
            'nilai_akhir' => 'required|numeric|min:0|max:100',
            'nilai_skala4' => 'nullable|numeric|min:0|max:4',
            'huruf' => 'nullable|string|max:2',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nama_mk_siakad.required' => 'Nama MK wajib diisi',
            'nilai_akhir.required' => 'Nilai akhir wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Auto create mahasiswa jika belum ada
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => $request->nim],
            ['nama' => $request->nim]
        );

        // Auto create/update pemetaan MK jika belum ada
        PemetaanMk::firstOrCreate(
            ['nama_mk_siakad' => $request->nama_mk_siakad],
            ['status_terverifikasi' => false]
        );

        // Auto calculate jika kosong
        $nilaiAkhir = $request->nilai_akhir;
        $nilaiSkala4 = $request->nilai_skala4 ?: $this->konversiNilaiSkala4($nilaiAkhir);
        $huruf = $request->huruf ?: $this->konversiNilaiHuruf($nilaiAkhir);

        $nilai->update([
            'nim' => $request->nim,
            'nama_mk_siakad' => $request->nama_mk_siakad,
            'tugas' => $request->tugas,
            'uts' => $request->uts,
            'uas' => $request->uas,
            'partisipatif' => $request->partisipatif,
            'nilai_akhir' => $nilaiAkhir,
            'nilai_skala4' => $nilaiSkala4,
            'huruf' => $huruf,
        ]);

        return redirect()->route('admin.nilai-mahasiswa.index')
            ->with('success', 'Nilai berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $nilai = NilaiMahasiswa::findOrFail($id);
        $nilai->delete();

        return redirect()->route('admin.nilai-mahasiswa.index')
            ->with('success', 'Nilai berhasil dihapus');
    }

    /**
     * Show form for bulk import
     */
    public function import()
    {
        return view('admin.nilai_mahasiswa.import');
    }

    /**
     * Konversi nilai ke skala 4
     */
    private function konversiNilaiSkala4($nilai)
    {
        if ($nilai >= 85) return 4.0;
        if ($nilai >= 80) return 3.7;
        if ($nilai >= 75) return 3.3;
        if ($nilai >= 70) return 3.0;
        if ($nilai >= 65) return 2.7;
        if ($nilai >= 60) return 2.3;
        if ($nilai >= 55) return 2.0;
        if ($nilai >= 50) return 1.7;
        if ($nilai >= 45) return 1.3;
        if ($nilai >= 40) return 1.0;
        return 0.0;
    }

    /**
     * Konversi nilai ke huruf
     */
    private function konversiNilaiHuruf($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'C-';
        if ($nilai >= 45) return 'D';
        return 'E';
    }
}
