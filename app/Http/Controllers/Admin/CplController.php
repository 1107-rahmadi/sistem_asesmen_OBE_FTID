<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\CplImport;
use App\Models\Cpl;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CplController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cpl::with('prodi');

        // Filter search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode_cpl', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        // Filter prodi
        if ($request->has('prodi_id') && $request->prodi_id) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $data['cpls'] = $query->orderBy('kode_cpl')->paginate(10);
        $data['search'] = $request->search;
        $data['filterProdi'] = $request->prodi_id;
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();

        // Import preview data from session
        $data['importData'] = Session::get('cpl_import_preview');
        $data['importProdiId'] = Session::get('cpl_import_prodi_id');

        return view('admin.cpl.index', $data);
    }

    /**
     * Download template
     */
    public function downloadTemplate()
    {
        $headers = ['Kode CPL', 'Deskripsi CPL'];
        $sampleData = [
            ['CPL-01', 'Deskripsi CPL 01'],
            ['CPL-02', 'Deskripsi CPL 02'],
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
            'Content-Disposition' => 'attachment; filename="template_import_cpl.csv"',
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
            $import = new CplImport();
            $import->parse($request->file('file')->getRealPath(), $request->prodi_id);
            $parsedData = $import->getParsedData();
            $summary = $import->getPreviewSummary();

            if (empty($parsedData)) {
                return redirect()->back()->with('error', 'File tidak memiliki data yang valid atau sheet "CPL Prodi" tidak ditemukan.');
            }

            Session::put('cpl_import_preview', $parsedData);
            Session::put('cpl_import_summary', $summary);
            Session::put('cpl_import_prodi_id', $request->prodi_id);

            return redirect()->route('admin.cpl.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Process and save import
     */
    public function processImport(Request $request)
    {
        $parsedData = Session::get('cpl_import_preview');
        $prodiId = Session::get('cpl_import_prodi_id');

        if (empty($parsedData) || !$prodiId) {
            return redirect()->route('admin.cpl.index')->with('error', 'Data import tidak ditemukan.');
        }

        try {
            $import = new CplImport();
            $stats = $import->processAndSave($prodiId);

            Session::forget(['cpl_import_preview', 'cpl_import_summary', 'cpl_import_prodi_id']);

            return redirect()->route('admin.cpl.index')->with('success',
                "Import berhasil! CPL baru: {$stats['created']}, CPL diupdate: {$stats['updated']}" .
                ($stats['skipped'] > 0 ? ", dilewati: {$stats['skipped']}" : "")
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Cancel import
     */
    public function cancelImport()
    {
        Session::forget(['cpl_import_preview', 'cpl_import_summary', 'cpl_import_prodi_id']);
        return redirect()->route('admin.cpl.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();
        return view('admin.cpl.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_cpl' => 'required|string|max:20',
            'prodi_id' => 'required|exists:prodi,id',
            'deskripsi' => 'required|string',
            'ambang_minimum' => 'required|numeric|min:0|max:100',
        ], [
            'kode_cpl.required' => 'Kode CPL wajib diisi',
            'prodi_id.required' => 'Prodi wajib dipilih',
            'prodi_id.exists' => 'Prodi tidak valid',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'ambang_minimum.required' => 'Ambang minimum wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Cpl::create([
            'kode_cpl' => $request->kode_cpl,
            'prodi_id' => $request->prodi_id,
            'deskripsi' => $request->deskripsi,
            'ambang_minimum' => $request->ambang_minimum,
        ]);

        return redirect()->route('admin.cpl.index')
            ->with('success', 'CPL berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cpl $cpl)
    {
        $cpl->load('cpmk');
        return view('admin.cpl.show', compact('cpl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cpl $cpl)
    {
        $data['prodis'] = Prodi::orderBy('kode_prodi')->get();
        $data['cpl'] = $cpl;
        return view('admin.cpl.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cpl $cpl)
    {
        $validator = Validator::make($request->all(), [
            'kode_cpl' => 'required|string|max:20',
            'prodi_id' => 'required|exists:prodi,id',
            'deskripsi' => 'required|string',
            'ambang_minimum' => 'required|numeric|min:0|max:100',
        ], [
            'kode_cpl.required' => 'Kode CPL wajib diisi',
            'prodi_id.required' => 'Prodi wajib dipilih',
            'prodi_id.exists' => 'Prodi tidak valid',
            'deskripsi.required' => 'Deskripsi wajib diisi',
            'ambang_minimum.required' => 'Ambang minimum wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cpl->update([
            'kode_cpl' => $request->kode_cpl,
            'prodi_id' => $request->prodi_id,
            'deskripsi' => $request->deskripsi,
            'ambang_minimum' => $request->ambang_minimum,
        ]);

        return redirect()->route('admin.cpl.index')
            ->with('success', 'CPL berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cpl $cpl)
    {
        // Check if CPL has related CPMK
        if ($cpl->cpmk()->count() > 0) {
            return redirect()->route('admin.cpl.index')
                ->with('error', 'CPL tidak bisa dihapus karena masih memiliki CPMK');
        }

        $cpl->delete();

        return redirect()->route('admin.cpl.index')
            ->with('success', 'CPL berhasil dihapus');
    }
}
