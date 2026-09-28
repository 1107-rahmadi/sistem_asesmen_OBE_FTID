<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PemetaanMk;
use App\Models\MatriksCpmk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemetaanMkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PemetaanMk::query();

        // Filter by verified status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status_terverifikasi', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_mk_siakad', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_mk', 'like', '%' . $request->search . '%');
            });
        }

        $data['pemetaan'] = $query->orderBy('nama_mk_siakad')->paginate(15);
        $data['search'] = $request->search;
        $data['filterStatus'] = $request->status;

        return view('admin.pemetaan_mk.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pemetaan_mk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_mk_siakad' => 'required|string|max:150',
            'kode_mk' => 'nullable|string|max:20',
            'status_terverifikasi' => 'boolean',
            'catatan' => 'nullable|string|max:255',
        ], [
            'nama_mk_siakad.required' => 'Nama MK wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PemetaanMk::create([
            'nama_mk_siakad' => $request->nama_mk_siakad,
            'kode_mk' => $request->kode_mk,
            'status_terverifikasi' => $request->has('status_terverifikasi'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.pemetaan-mk.index')
            ->with('success', 'Pemetaan MK berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(PemetaanMk $pemetaanMk)
    {
        // Ambil data matriks CPMK yang terkait
        $matriksRelated = MatriksCpmk::where('nama_mk_matrix', $pemetaanMk->nama_mk_siakad)
            ->with('cpmk.cpl')
            ->get();

        return view('admin.pemetaan_mk.show', compact('pemetaanMk', 'matriksRelated'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PemetaanMk $pemetaanMk)
    {
        return view('admin.pemetaan_mk.edit', compact('pemetaanMk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PemetaanMk $pemetaanMk)
    {
        $validator = Validator::make($request->all(), [
            'nama_mk_siakad' => 'required|string|max:150',
            'kode_mk' => 'nullable|string|max:20',
            'status_terverifikasi' => 'boolean',
            'catatan' => 'nullable|string|max:255',
        ], [
            'nama_mk_siakad.required' => 'Nama MK wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pemetaanMk->update([
            'nama_mk_siakad' => $request->nama_mk_siakad,
            'kode_mk' => $request->kode_mk,
            'status_terverifikasi' => $request->has('status_terverifikasi'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.pemetaan-mk.index')
            ->with('success', 'Pemetaan MK berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PemetaanMk $pemetaanMk)
    {
        $pemetaanMk->delete();

        return redirect()->route('admin.pemetaan-mk.index')
            ->with('success', 'Pemetaan MK berhasil dihapus');
    }
}
