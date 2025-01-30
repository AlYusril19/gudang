<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'apiLogin']);

// Route::middleware('auth:sanctum')->put('/user/update', [UserController::class, 'apiUpdate']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/get-barang', [BarangController::class, 'getBarangs']);
    Route::get('/get-barang-kembali', [BarangController::class, 'getBarangsKembali']);
    Route::get('/get-customer', [CustomerController::class, 'getCustomers']);
    Route::get('/get-user/{id}', [UserController::class, 'apiUser']);
    Route::put('/user/update', [UserController::class, 'apiUserUpdate']);
    Route::get('/get-teknisi/{id}', [UserController::class, 'apiTeknisi']);
    Route::post('/penjualan', [PenjualanController::class, 'storeApi']);
    Route::post('/penjualan-destroy', [PenjualanController::class, 'destroyApi']);
    Route::get('/get-penjualan/{id}', [PenjualanController::class, 'getPenjualanApi']);
    Route::get('/get-penjualan-by-id/{id}', [PenjualanController::class, 'getPenjualanApiById']);
    Route::get('/get-penjualan-mitra/{id}', [PenjualanController::class, 'getPenjualanMitraApi']);
    Route::post('/pembelian', [PembelianController::class, 'storeApi']);
    Route::post('/pembelian-destroy', [PembelianController::class, 'destroyApi']);
    Route::get('/get-pembelian/{id}', [PembelianController::class, 'getPembelianApi']);
});
    // Route::get('/get-barang-kembali', [BarangController::class, 'getBarangsKembali']);
    // Route::get('/get-penjualan/{id}', [PenjualanController::class, 'getPenjualanApi']);
    // Route::get('/get-penjualan-mitra/{id}', [PenjualanController::class, 'getPenjualanMitraApi']);
    // Route::get('/get-pembelian/{id}', [PembelianController::class, 'getPembelianApi']);

// Route::get('/get-teknisi/{id}', [UserController::class, 'apiTeknisi']);
// Route::get('/get-barang', [BarangController::class, 'getBarangs']);