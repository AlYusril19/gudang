<?php

use App\Http\Controllers\AdminBerandaController;
use App\Http\Controllers\Api\StokBarangController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OperatorBerandaController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'showLoginForm'])->name('showLoginForm');


// Route::get('/', function () {
//     return view('auth.login');
// });

Auth::routes();

// Role User Admin
Route::group(['middleware' => ['auth', 'role:admin,superadmin']], function () {
    // Route untuk admin
    // Route::get('/admin/dashboard', [AdminBerandaController::class, 'index'])->name('admin.index');
    Route::resource('/admin/dashboard', AdminBerandaController::class);

    Route::resource('/admin/users', UserController::class);
    Route::post('/admin/users/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');

    Route::resource('/admin/barang', BarangController::class);
    Route::post('/admin/barang/toggle-status', [BarangController::class, 'toggleStatus'])->name('barang.toggleStatus');
    Route::delete('/delete-image/{id}', [BarangController::class, 'deleteImage'])->name('delete-image');
    
    Route::resource('/admin/kategori', KategoriController::class);

    Route::resource('/admin/pembelian', PembelianController::class);
    Route::get('/admin/barang-masuk', [PembelianController::class, 'indexBarangMasuk'])->name('pembelian.indexBarangMasuk');
    Route::get('/pembelian/getHargaBeli', [PembelianController::class, 'getHargaBeli'])->name('pembelian.getHargaBeli');

    Route::resource('/admin/penjualan', PenjualanController::class);
    Route::get('/admin/barang-keluar', [PenjualanController::class, 'indexBarangKeluar'])->name('penjualan.indexBarangKeluar');
    Route::post('/get-harga-jual', [PenjualanController::class, 'getHargaJual']);
    Route::get('/get-stok/{id}', [PenjualanController::class, 'getStok']);

    Route::resource('/admin/customer', CustomerController::class);

    Route::resource('/admin/supplier', SupplierController::class);
    Route::resource('/admin/setting', SettingController::class);
});

Route::group(['middleware' => ['auth', 'role:staff']], function () {
    // Route untuk staff
    Route::get('/staff', [OperatorBerandaController::class, 'index']);
});

Route::get('/api/barangs', [BarangController::class, 'getBarangs']);