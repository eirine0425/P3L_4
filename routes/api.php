<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\KategoriBarangController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\DetailTransaksiController;
use App\Http\Controllers\Api\GaransiController;
use App\Http\Controllers\Api\KeranjangBelanjaController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\PembeliController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\WebViewController;
use App\Http\Controllers\Api\DiskusiProdukController;
use App\Http\Controllers\Api\KomisiController;
use App\Http\Controllers\Api\MerchController;
use App\Http\Controllers\Api\PengirimanController;
use App\Http\Controllers\Api\RequestDonasiController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TransaksiMerchController;
use App\Http\Controllers\Api\TransaksiPenitipanController;
use App\Http\Controllers\Api\AlamatController;

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

// Rute Autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');

// Rute Web View
Route::get('/beranda', [WebViewController::class, 'beranda']);
Route::get('/produk', [WebViewController::class, 'daftarProduk']);
Route::get('/produk/{id}', [WebViewController::class, 'tampilProduk']);
Route::get('/garansi/cek', [WebViewController::class, 'cekGaransi']);
Route::get('/tentang-kami', [WebViewController::class, 'tentangKami']);
Route::get('/keranjang', [WebViewController::class, 'keranjang'])->middleware('auth:api');
Route::post('/keranjang/tambah', [WebViewController::class, 'tambahKeKeranjang'])->middleware('auth:api');
Route::post('/keranjang/hapus', [WebViewController::class, 'hapusDariKeranjang'])->middleware('auth:api');
Route::get('/checkout', [WebViewController::class, 'checkout'])->middleware('auth:api');
Route::post('/checkout/proses', [WebViewController::class, 'prosesCheckout'])->middleware('auth:api');

// Dashboard Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard/pemilik', [WebViewController::class, 'dashboardPemilik'])->middleware('role:owner');
    Route::get('/dashboard/admin', [WebViewController::class, 'dashboardAdmin'])->middleware('role:admin');
    Route::get('/dashboard/gudang', [WebViewController::class, 'dashboardGudang'])->middleware('role:pegawai gudang');
    Route::get('/dashboard/cs', [WebViewController::class, 'dashboardCS'])->middleware('role:cs');
    Route::get('/dashboard/penitip', [WebViewController::class, 'dashboardPenitip'])->middleware('role:penitip');
    Route::get('/dashboard/pembeli', [WebViewController::class, 'dashboardPembeli'])->middleware('role:pembeli');
    Route::get('/dashboard/organisasi', [WebViewController::class, 'dashboardOrganisasi'])->middleware('role:organisasi');
});

// Rute CRUD untuk Alamat
Route::apiResource('alamat', AlamatController::class);

// Rute CRUD untuk Barang
Route::apiResource('barang', BarangController::class);

// Rute CRUD untuk DetailTransaksi
Route::apiResource('detail-transaksi', DetailTransaksiController::class);

// Rute CRUD untuk DiskusiProduk
Route::apiResource('diskusi-produk', DiskusiProdukController::class);

// Rute CRUD untuk Donasi
Route::apiResource('donasi', DonasiController::class);

// Rute CRUD untuk Garansi
Route::apiResource('garansi', GaransiController::class);

// Rute CRUD untuk KategoriBarang
Route::apiResource('kategori-barang', KategoriBarangController::class);

// Rute CRUD untuk KeranjangBelanja
Route::apiResource('keranjang-belanja', KeranjangBelanjaController::class);

// Rute CRUD untuk Komisi
Route::apiResource('komisi', KomisiController::class);

// Rute CRUD untuk Merch
Route::apiResource('merch', MerchController::class);

// Rute CRUD untuk Organisasi
Route::apiResource('organisasi', OrganisasiController::class);

// Rute CRUD untuk Pegawai
Route::apiResource('pegawai', PegawaiController::class);

// Rute CRUD untuk Pembeli
Route::apiResource('pembeli', PembeliController::class);

// Rute CRUD untuk Pengiriman
Route::apiResource('pengiriman', PengirimanController::class);

// Rute CRUD untuk Penitip
Route::apiResource('penitip', PenitipController::class);

// Rute CRUD untuk RequestDonasi
Route::apiResource('request-donasi', RequestDonasiController::class);

// Rute CRUD untuk Role
Route::apiResource('role', RoleController::class);

// Rute CRUD untuk Transaksi
Route::apiResource('transaksi', TransaksiController::class);

// Rute CRUD untuk TransaksiMerch
Route::apiResource('transaksi-merch', TransaksiMerchController::class);

// Rute CRUD untuk TransaksiPenitipan
Route::apiResource('transaksi-penitipan', TransaksiPenitipanController::class);

// Rute CRUD untuk User
Route::apiResource('users', UserController::class);
