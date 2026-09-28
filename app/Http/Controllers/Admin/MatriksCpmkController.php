<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\MatriksCpmkImport;
use App\Models\Cpmk;
use App\Models\MatriksCpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MatriksCpmkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MatriksCpmk::with('cpmk.cpl');

        // Filter by CPMK
        if ($request->has('cpmk_id') && $request->cpmk_id) {
            $query->where('cpmk_id', $request->cpmk_id);
        }

        // Filter by Kode MK
        if ($request->has('kode_mk') && $request->kode_mk) {
            $query->where('kode_mk', 'like', '%' . $request->kode_mk . '%');
        }

        // Filter search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_mk', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_mk_matrix', 'like', '%' . $request->search . '%');
            });
        }

        $data['matriks'] = $query->orderBy('kode_mk')->paginate(15);
        $data['cpmks'] = Cpmk::with('cpl')->orderBy('kode_cpmk')->get();
        $data['search'] = $request->search;
        $data['filterCpmk'] = $request->cpmk_id;
        $data['filterKodeMk'] = $request->kode_mk;

        // Import preview data from session
        $data['importData'] = Session::get('matriks_cpmk_import_preview');

        return view('admin.matriks_cpmk.index', $data);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $headers = ['CPL', 'Kode MK', 'Nama MK', 'Kode CPMK', 'Deskripsi CPMK', 'Skor', 'Part', 'Tugas', 'UTS', 'UAS'];
        $sampleData = [
            ['CPL-01', 'MK01', 'Etika Profesi', 'CPMK.01.1', 'Mampu menganalisis isu etika', 100, 10, 30, 30, 30],
            ['CPL-01', 'MK09', 'Komunikasi Data', 'CPMK.01.1', 'Mampu menganalisis isu etika', 100, 15, 35, 25, 25],
            ['CPL-02', 'MK13', 'Jaringan Komputer', 'CPMK.02.1', 'Mampu merancang jaringan', 100, 20, 30, 25, 25],
        ];

        $output = fopen('php://temp', 'r+');
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, $headers, ';');
        foreach ($sampleData as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_matriks_cpmk.csv"',
        ]);
    }

    /**
     * Upload and preview import
     */
    public function uploadImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'file.required' => 'File harus diupload',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
        ]);

        try {
            $import = new MatriksCpmkImport();
            $parsedData = $import->parse($request->file('file')->getRealPath());
            $summary = $import->getSummary();

            if (empty($parsedData)) {
                return redirect()->back()->with('error', 'File tidak valid atau tidak ada data yang bisa diproses.');
            }

            Session::put('matriks_cpmk_import_preview', $parsedData);
            Session::put('matriks_cpmk_import_summary', $summary);

            return redirect()->route('admin.matriks-cpmk.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Process and save import
     */
    public function processImport(Request $request)
    {
        $parsedData = Session::get('matriks_cpmk_import_preview');

        if (empty($parsedData)) {
            return redirect()->route('admin.matriks-cpmk.index')->with('error', 'Data import tidak ditemukan.');
        }

        try {
            $import = new MatriksCpmkImport();
            $import->setData($parsedData);
            $stats = $import->save();

            Session::forget(['matriks_cpmk_import_preview', 'matriks_cpmk_import_summary']);

            return redirect()->route('admin.matriks-cpmk.index')->with('success',
                "Import selesai! Baru: {$stats['created']}, Update: {$stats['updated']}" .
                ($stats['skipped'] > 0 ? ", Dilewati: {$stats['skipped']}" : "")
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel import
     */
    public function cancelImport()
    {
        Session::forget(['matriks_cpmk_import_preview', 'matriks_cpmk_import_summary']);
        return redirect()->route('admin.matriks-cpmk.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['cpmks'] = Cpmk::with('cpl')->orderBy('kode_cpmk')->get();
        return view('admin.matriks_cpmk.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cpmk_id' => 'required|exists:cpmk,id',
            'kode_mk' => 'required|string|max:20',
            'nama_mk_matrix' => 'nullable|string|max:150',
            'bobot_partisipatif' => 'nullable|numeric|min:0',
            'bobot_tugas' => 'nullable|numeric|min:0',
            'bobot_uts' => 'nullable|numeric|min:0',
            'bobot_uas' => 'nullable|numeric|min:0',
            'skor_maksimal' => 'required|numeric|min:0',
        ], [
            'cpmk_id.required' => 'CPMK wajib dipilih',
            'kode_mk.required' => 'Kode MK wajib diisi',
            'skor_maksimal.required' => 'Skor maksimal wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MatriksCpmk::create([
            'cpmk_id' => $request->cpmk_id,
            'kode_mk' => $request->kode_mk,
            'nama_mk_matrix' => $request->nama_mk_matrix,
            'bobot_partisipatif' => $request->bobot_partisipatif ?? 0,
            'bobot_tugas' => $request->bobot_tugas ?? 0,
            'bobot_uts' => $request->bobot_uts ?? 0,
            'bobot_uas' => $request->bobot_uas ?? 0,
            'skor_maksimal' => $request->skor_maksimal,
        ]);

        return redirect()->route('admin.matriks-cpmk.index')
            ->with('success', 'Matriks CPMK berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MatriksCpmk $matriksCpmk)
    {
        $matriksCpmk->load('cpmk.cpl');
        return view('admin.matriks_cpmk.show', compact('matriksCpmk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MatriksCpmk $matriksCpmk)
    {
        $data['cpmks'] = Cpmk::with('cpl')->orderBy('kode_cpmk')->get();
        $data['matriks'] = $matriksCpmk;
        return view('admin.matriks_cpmk.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MatriksCpmk $matriksCpmk)
    {
        $validator = Validator::make($request->all(), [
            'cpmk_id' => 'required|exists:cpmk,id',
            'kode_mk' => 'required|string|max:20',
            'nama_mk_matrix' => 'nullable|string|max:150',
            'bobot_partisipatif' => 'nullable|numeric|min:0',
            'bobot_tugas' => 'nullable|numeric|min:0',
            'bobot_uts' => 'nullable|numeric|min:0',
            'bobot_uas' => 'nullable|numeric|min:0',
            'skor_maksimal' => 'required|numeric|min:0',
        ], [
            'cpmk_id.required' => 'CPMK wajib dipilih',
            'kode_mk.required' => 'Kode MK wajib diisi',
            'skor_maksimal.required' => 'Skor maksimal wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $matriksCpmk->update([
            'cpmk_id' => $request->cpmk_id,
            'kode_mk' => $request->kode_mk,
            'nama_mk_matrix' => $request->nama_mk_matrix,
            'bobot_partisipatif' => $request->bobot_partisipatif ?? 0,
            'bobot_tugas' => $request->bobot_tugas ?? 0,
            'bobot_uts' => $request->bobot_uts ?? 0,
            'bobot_uas' => $request->bobot_uas ?? 0,
            'skor_maksimal' => $request->skor_maksimal,
        ]);

        return redirect()->route('admin.matriks-cpmk.index')
            ->with('success', 'Matriks CPMK berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MatriksCpmk $matriksCpmk)
    {
        $matriksCpmk->delete();

        return redirect()->route('admin.matriks-cpmk.index')
            ->with('success', 'Matriks CPMK berhasil dihapus');
    }
}
