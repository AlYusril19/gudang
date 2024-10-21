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
    Route::get('/get-customer', [CustomerController::class, 'getCustomers']);
    Route::get('/get-user/{id}', [UserController::class, 'apiUser']);
    Route::put('/user/update', [UserController::class, 'apiUpdate']);
    Route::post('/penjualan', [PenjualanController::class, 'storeApi']);
    Route::post('/pembelian', [PembelianController::class, 'storeApi']);
});
    Route::get('/get-barang-kembali', [BarangController::class, 'getBarangsKembali']);

// Route::get('/get-user/{id}', [UserController::class, 'apiUser']);
// Route::get('/get-barang', [BarangController::class, 'getBarangs']);