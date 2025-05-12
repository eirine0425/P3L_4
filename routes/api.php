<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\DonasiController; 
use App\Http\Controllers\Api\GaransiController;
use App\Http\Controllers\Api\KategoriBarangController;
use App\Http\Controllers\Api\MerchController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\PembeliController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/users', [UserController::class, 'index']);

Route::prefix('users')->group(function () {
    //http://127.0.0.1:8000/api/usersy

Route::post('/users', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::prefix('barang')->group(function () {
    Route::get('/', [BarangController::class, 'index']);
    Route::post('/', [BarangController::class, 'store']);
    Route::get('/{id}', [BarangController::class, 'show']);
    Route::put('/{id}', [BarangController::class, 'update']);
    Route::delete('/{id}', [BarangController::class, 'destroy']);
});

Route::prefix('donasi')->group(function () {
    Route::get('/', [DonasiController::class, 'index']);
    Route::post('/', [DonasiController::class, 'store']);
    Route::get('/{id}', [DonasiController::class, 'show']);
    Route::put('/{id}', [DonasiController::class, 'update']);
    Route::delete('/{id}', [DonasiController::class, 'destroy']);
});

Route::prefix('garansi')->group(function () {
    Route::get('/', [GaransiController::class, 'index']);
    Route::post('/', [GaransiController::class, 'store']);
    Route::get('/{id}', [GaransiController::class, 'show']);
    Route::put('/{id}', [GaransiController::class, 'update']);
    Route::delete('/{id}', [GaransiController::class, 'destroy']);
});

Route::prefix('kategori_barang')->group(function () {
    Route::get('/', [KategoriBarangController::class, 'index']);
    Route::post('/', [KategoriBarangController::class, 'store']);
    Route::get('/{id}', [KategoriBarangController::class, 'show']);
    Route::put('/{id}', [KategoriBarangController::class, 'update']);
    Route::delete('/{id}', [KategoriBarangController::class, 'destroy']);
});

Route::prefix('merch')->group(function () {
    Route::get('/', [MerchController::class, 'index']);
    Route::post('/', [MerchController::class, 'store']);
    Route::get('/{id}', [MerchController::class, 'show']);
    Route::put('/{id}', [MerchController::class, 'update']);
    Route::delete('/{id}', [MerchController::class, 'destroy']);
});

Route::prefix('pegawai')->group(function () {
    Route::get('/', [PegawaiController::class, 'index']);
    Route::post('/', [PegawaiController::class, 'store']);
    Route::get('/{id}', [PegawaiController::class, 'show']);
    Route::put('/{id}', [PegawaiController::class, 'update']);
    Route::delete('/{id}', [PegawaiController::class, 'destroy']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api',"multiRole:CS,Admin")->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('alamat')->group(function () {
    Route::get('/', [AlamatController::class, 'index']);
    Route::post('/', [AlamatController::class, 'store']);
    Route::get('/{id}', [AlamatController::class, 'show']);
    Route::put('/{id}', [AlamatController::class, 'update']);
    Route::delete('/{id}', [AlamatController::class, 'destroy']);
});

Route::prefix('organisasi')->group(function () {
    Route::get('/', [OrganisasiController::class, 'index']);
    Route::post('/', [OrganisasiController::class, 'store']);
    Route::get('/{id}', [OrganisasiController::class, 'show']);
    Route::put('/{id}', [OrganisasiController::class, 'update']);
    Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
});

Route::prefix('pembeli')->group(function () {
    Route::get('/', [PembeliController::class, 'index']);
    Route::post('/', [PembeliController::class, 'store']);
    Route::get('/{id}', [PembeliController::class, 'show']);
    Route::put('/{id}', [PembeliController::class, 'update']);
    Route::delete('/{id}', [PembeliController::class, 'destroy']);
});

Route::prefix('penitip')->group(function () {
    Route::get('/', [PenitipController::class, 'index']);
    Route::post('/', [PenitipController::class, 'store']);
    Route::get('/{id}', [PenitipController::class, 'show']);
    Route::put('/{id}', [PenitipController::class, 'update']);
    Route::delete('/{id}', [PenitipController::class, 'destroy']);
});

Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::put('/{id}', [RoleController::class, 'update']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});
