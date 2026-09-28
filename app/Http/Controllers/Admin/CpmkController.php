<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CpmkImport;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CpmkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cpmk::with(['cpl.prodi', 'matriksCpmk']);

        // Filter by prodi
        if ($request->has('prodi_id') && $request->prodi_id) {
            $query->whereHas('cpl', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        // Filter by CPL
        if ($request->has('cpl_id') && $request->cpl_id) {
            $query->where('cpl_id', $request->cpl_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_cpmk', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $data['cpmks'] = $query->orderBy('kode_cpmk')->paginate(10);
        $data['search'] = $request->search;
        $data['filterProdi'] = $request->prodi_id;
        $data['filterCpl'] = $request->cpl_id;
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();
        $data['cpls'] = Cpl::with('prodi')->orderBy('kode_cpl')->get();

        // Import preview data from session
        $data['importData'] = Session::get('cpmk_import_preview');
        $data['importProdiId'] = Session::get('cpmk_import_prodi_id');

        return view('admin.cpmk.index', $data);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $headers = ['CPL', 'Kode CPMK', 'CPMK'];
        $sampleData = [
            ['CPL-01', 'CPMK.01.1', 'Deskripsi CPMK 01.1 - Matakuliah X'],
            ['CPL-01', 'CPMK.01.2', 'Deskripsi CPMK 01.2 - Matakuliah Y'],
            ['CPL-02', 'CPMK.02.1', 'Deskripsi CPMK 02.1 - Matakuliah Z'],
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
            'Content-Disposition' => 'attachment; filename="template_import_cpmk.csv"',
        ]);
    }

    /**
     * Upload and preview import
     */
    public function uploadImport(Request $request)
    {
        $request->validate([
            'prodi_id' => 'required|exists:prodi,id',
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ], [
            'prodi_id.required' => 'Prodi wajib dipilih',
            'prodi_id.exists' => 'Prodi tidak valid',
            'file.required' => 'File harus diupload',
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv',
        ]);

        try {
            $import = new CpmkImport();
            $parsedData = $import->parse($request->file('file')->getRealPath(), $request->prodi_id);
            $summary = $import->getSummary();

            if (empty($parsedData)) {
                return redirect()->back()->with('error', 'File tidak valid atau sheet "Pemetaan CPL-CPMK-MK" tidak ditemukan.');
            }

            Session::put('cpmk_import_preview', $parsedData);
            Session::put('cpmk_import_summary', $summary);
            Session::put('cpmk_import_prodi_id', $request->prodi_id);

            return redirect()->route('admin.cpmk.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Process and save import
     */
    public function processImport(Request $request)
    {
        $parsedData = Session::get('cpmk_import_preview');
        $prodiId = Session::get('cpmk_import_prodi_id');

        if (empty($parsedData) || !$prodiId) {
            return redirect()->route('admin.cpmk.index')->with('error', 'Data import tidak ditemukan.');
        }

        try {
            $import = new CpmkImport();
            $import->setData($parsedData);
            $stats = $import->save();

            Session::forget(['cpmk_import_preview', 'cpmk_import_summary', 'cpmk_import_prodi_id']);

            return redirect()->route('admin.cpmk.index')->with('success',
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
        Session::forget(['cpmk_import_preview', 'cpmk_import_summary', 'cpmk_import_prodi_id']);
        return redirect()->route('admin.cpmk.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();
        $data['cpls'] = Cpl::with('prodi')->orderBy('kode_cpl')->get();
        return view('admin.cpmk.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cpl_id' => 'required|exists:cpl,id',
            'kode_cpmk' => 'required|string|max:30|unique:cpmk,kode_cpmk',
            'deskripsi' => 'nullable|string',
            'ambang_minimum' => 'required|numeric|min:0|max:100',
        ], [
            'cpl_id.required' => 'CPL wajib dipilih',
            'kode_cpmk.required' => 'Kode CPMK wajib diisi',
            'kode_cpmk.unique' => 'Kode CPMK sudah ada',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Cpmk::create([
            'cpl_id' => $request->cpl_id,
            'kode_cpmk' => $request->kode_cpmk,
            'deskripsi' => $request->deskripsi,
            'ambang_minimum' => $request->ambang_minimum,
        ]);

        return redirect()->route('admin.cpmk.index')->with('success', 'CPMK berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cpmk $cpmk)
    {
        $cpmk->load(['cpl.prodi', 'matriksCpmk']);
        return view('admin.cpmk.show', compact('cpmk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cpmk $cpmk)
    {
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();
        $data['cpls'] = Cpl::with('prodi')->orderBy('kode_cpl')->get();
        $data['cpmk'] = $cpmk;
        return view('admin.cpmk.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cpmk $cpmk)
    {
        $validator = Validator::make($request->all(), [
            'cpl_id' => 'required|exists:cpl,id',
            'kode_cpmk' => 'required|string|max:30|unique:cpmk,kode_cpmk,' . $cpmk->id,
            'deskripsi' => 'nullable|string',
            'ambang_minimum' => 'required|numeric|min:0|max:100',
        ], [
            'cpl_id.required' => 'CPL wajib dipilih',
            'kode_cpmk.required' => 'Kode CPMK wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cpmk->update([
            'cpl_id' => $request->cpl_id,
            'kode_cpmk' => $request->kode_cpmk,
            'deskripsi' => $request->deskripsi,
            'ambang_minimum' => $request->ambang_minimum,
        ]);

        return redirect()->route('admin.cpmk.index')->with('success', 'CPMK berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cpmk $cpmk)
    {
        if ($cpmk->matriksCpmk()->count() > 0) {
            return redirect()->route('admin.cpmk.index')->with('error', 'CPMK tidak bisa dihapus karena masih memiliki Matriks CPMK');
        }

        $cpmk->delete();
        return redirect()->route('admin.cpmk.index')->with('success', 'CPMK berhasil dihapus');
    }
}
