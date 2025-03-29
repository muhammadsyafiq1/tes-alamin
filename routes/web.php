<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'halamanLogin'])->name('halaman-login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Peserta Routes
Route::middleware('can:aksesBPRS')->group(function () {
    Route::get('/insert-peserta', [HomeController::class, 'insertPeserta'])->name('insert-peserta');
    Route::get('/list-peserta-data-diterima', [HomeController::class, 'listPesertaDataDiterima']);
    Route::get('/peserta/covernote/{id}', [HomeController::class, 'cetakCovernote'])->name('cetak-covernote');
});

Route::middleware('can:aksesAdmin')->group(function () {
    Route::get('/list-peserta-pending', [HomeController::class, 'listPesertaPending'])->name('list-peserta-pending');
    Route::get('/list-peserta-upload-dokumen', [HomeController::class, 'listPesertaUploadDokumen']);
});

Route::get('/peserta/edit/{id}', [HomeController::class, 'editPeserta']);
Route::get('/list-peserta-terima-data-dokumen', [HomeController::class, 'listPesertaTerimaDataDokumen'])->name('list-peserta-diterima');
Route::get('/list-peserta-data-ditolak', [HomeController::class, 'listPesertaDataDitolak']);
Route::get('/list-peserta-dokumen-ditolak', [HomeController::class, 'listPesertaDokumenDitolak']);

// Peserta Actions
Route::post('/peserta/upload-dokumen', [HomeController::class, 'uploadDokumen'])->name('upload-dokumen');
Route::post('/peserta/store', [HomeController::class, 'storePeserta'])->name('peserta.store');
Route::post('/peserta/update/{id}', [HomeController::class, 'storePeserta'])->name('peserta.update');
Route::post('/peserta/approve', [HomeController::class, 'approvePeserta']);
Route::post('/peserta/approve-dokumen', [HomeController::class, 'approvePesertaDokumen']);




