<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Halaman Welcome (jika belum login)
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (Login, Register, etc)
Auth::routes();

// Home redirect ke dashboard admin jika sudah login
Route::get('/home', function () {
    return redirect()->route('admin.cpl.index');
})->name('home')->middleware('auth');

// Admin Routes (Harus Login)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // CPL Routes (with custom import routes)
    Route::get('/cpl/template', [\App\Http\Controllers\Admin\CplController::class, 'downloadTemplate'])->name('cpl.template');
    Route::post('/cpl/upload-import', [\App\Http\Controllers\Admin\CplController::class, 'uploadImport'])->name('cpl.upload-import');
    Route::post('/cpl/process-import', [\App\Http\Controllers\Admin\CplController::class, 'processImport'])->name('cpl.process-import');
    Route::get('/cpl/cancel-import', [\App\Http\Controllers\Admin\CplController::class, 'cancelImport'])->name('cpl.cancel-import');
    Route::resource('cpl', \App\Http\Controllers\Admin\CplController::class);

    // CPMK Routes (with import)
    Route::get('/cpmk/template', [\App\Http\Controllers\Admin\CpmkController::class, 'downloadTemplate'])->name('cpmk.template');
    Route::post('/cpmk/upload-import', [\App\Http\Controllers\Admin\CpmkController::class, 'uploadImport'])->name('cpmk.upload-import');
    Route::post('/cpmk/process-import', [\App\Http\Controllers\Admin\CpmkController::class, 'processImport'])->name('cpmk.process-import');
    Route::get('/cpmk/cancel-import', [\App\Http\Controllers\Admin\CpmkController::class, 'cancelImport'])->name('cpmk.cancel-import');
    Route::resource('cpmk', \App\Http\Controllers\Admin\CpmkController::class);

    // Matriks CPMK Routes
    Route::get('/matriks-cpmk/template', [\App\Http\Controllers\Admin\MatriksCpmkController::class, 'downloadTemplate'])->name('matriks-cpmk.template');
    Route::post('/matriks-cpmk/upload-import', [\App\Http\Controllers\Admin\MatriksCpmkController::class, 'uploadImport'])->name('matriks-cpmk.upload-import');
    Route::post('/matriks-cpmk/process-import', [\App\Http\Controllers\Admin\MatriksCpmkController::class, 'processImport'])->name('matriks-cpmk.process-import');
    Route::get('/matriks-cpmk/cancel-import', [\App\Http\Controllers\Admin\MatriksCpmkController::class, 'cancelImport'])->name('matriks-cpmk.cancel-import');
    Route::resource('matriks-cpmk', \App\Http\Controllers\Admin\MatriksCpmkController::class)->parameters([
        'matriks-cpmk' => 'matriksCpmk'
    ]);

    // Import Master Routes
    Route::get('/import-master', [\App\Http\Controllers\Admin\ImportMasterController::class, 'index'])->name('import-master.index');
    Route::post('/import-master/upload', [\App\Http\Controllers\Admin\ImportMasterController::class, 'upload'])->name('import-master.upload');
    Route::post('/import-master/process', [\App\Http\Controllers\Admin\ImportMasterController::class, 'process'])->name('import-master.process');
    Route::get('/import-master/template', [\App\Http\Controllers\Admin\ImportMasterController::class, 'downloadTemplate'])->name('import-master.template');
    Route::get('/import-master/clear', [\App\Http\Controllers\Admin\ImportMasterController::class, 'clear'])->name('import-master.clear');

    // Pemetaan MK Routes
    Route::resource('pemetaan-mk', \App\Http\Controllers\Admin\PemetaanMkController::class)->parameters([
        'pemetaan-mk' => 'pemetaanMk'
    ]);

    // Mahasiswa Routes
    Route::resource('mahasiswa', \App\Http\Controllers\Admin\MahasiswaController::class);

    // Nilai Mahasiswa Routes
    Route::resource('nilai-mahasiswa', \App\Http\Controllers\Admin\NilaiMahasiswaController::class);

    // Capaian CPL Routes
    Route::get('/capaian-cpl', [\App\Http\Controllers\Admin\CapaianCplController::class, 'index'])->name('capaian-cpl.index');
    Route::post('/capaian-cpl/recalculate', [\App\Http\Controllers\Admin\CapaianCplController::class, 'recalculate'])->name('capaian-cpl.recalculate');
    Route::get('/capaian-cpl/{nim}/detail', [\App\Http\Controllers\Admin\CapaianCplController::class, 'detail'])->name('capaian-cpl.detail');
    Route::get('/capaian-cpl/{nim}/chart', [\App\Http\Controllers\Admin\CapaianCplController::class, 'getChartData'])->name('capaian-cpl.chart');
});
