<?php

use App\Http\Controllers\AdminBerandaController;
use App\Http\Controllers\Api\StokBarangController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OperatorBerandaController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    // Route untuk admin
    Route::get('/admin/dashboard', [AdminBerandaController::class, 'index'])->name('admin.index');

    Route::get('/admin/users/index', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/user/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/user', [UserController::class, 'store'])->name('users.store');
    Route::delete('/admin/user/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/admin/barangs/index', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/admin/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/admin/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::delete('/admin/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::get('/admin/barang/{id}/show', [BarangController::class, 'show'])->name('barang.show');
    Route::get('/admin/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/admin/barang/{id}/edit', [BarangController::class, 'update'])->name('barang.update');

    Route::get('/admin/barang_masuk/index', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::get('/admin/barang_masuk/create', [PembelianController::class, 'create'])->name('pembelian.create');
    Route::post('/admin/barang_masuk', [PembelianController::class, 'store'])->name('pembelian.store');
    Route::get('/admin/barang_masuk/{id}/edit', [PembelianController::class, 'edit'])->name('pembelian.edit');
    Route::put('/admin/barang_masuk/{id}/edit', [PembelianController::class, 'update'])->name('pembelian.update');
    Route::delete('/admin/barang_masuk/{id}', [PembelianController::class, 'destroy'])->name('pembelian.destroy');

    Route::get('/admin/barang_keluar/index', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/admin/barang_keluar/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/admin/barang_keluar', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::post('/get-harga-jual', [PenjualanController::class, 'getHargaJual']);
    Route::delete('/admin/barang_keluar/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');

    Route::get('/get-stok/{id}', [PenjualanController::class, 'getStok']);

    Route::get('/admin/customer/index', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/admin/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/admin/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::delete('/admin/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    Route::get('/admin/supplier/index', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/admin/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
    Route::post('/admin/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::delete('/admin/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

});

Route::group(['middleware' => ['auth', 'role:operator']], function () {
    // Route untuk operator
    Route::get('/operator', [OperatorBerandaController::class, 'index']);
});
