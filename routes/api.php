<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\DetailTransaksiController;
use App\Http\Controllers\Api\DiskusiProdukController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\GaransiController;
use App\Http\Controllers\Api\KategoriBarangController;
use App\Http\Controllers\Api\KeranjangBelanjaController;
use App\Http\Controllers\Api\KomisiController;
use App\Http\Controllers\Api\MerchController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PembeliController;
use App\Http\Controllers\Api\PengirimanController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\TransaksiMerchController;
use App\Http\Controllers\Api\TransaksiPenitipanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WebViewController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public view routes (tidak memerlukan autentikasi)
Route::get('/', [WebViewController::class, 'home']);
Route::get('/products', [WebViewController::class, 'products']);
Route::get('/products/{id}', [WebViewController::class, 'productDetail']);
Route::get('/warranty-check', [WebViewController::class, 'warrantyCheck']);
Route::get('/about', [WebViewController::class, 'about']);

// Auth view routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::get('/password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard routes
    Route::get('/dashboard', [WebViewController::class, 'dashboard'])->name('dashboard');
    
    // Role-specific dashboard routes
    Route::middleware('role:Owner')->group(function () {
        Route::get('/dashboard/owner', [WebViewController::class, 'ownerDashboard']);
        Route::get('/dashboard/owner/reports', [WebViewController::class, 'ownerReports']);
    });
    
    Route::middleware('role:Admin')->group(function () {
        Route::get('/dashboard/admin', [WebViewController::class, 'adminDashboard']);
        Route::get('/dashboard/admin/users', [WebViewController::class, 'adminUsers']);
        Route::get('/dashboard/admin/roles', [WebViewController::class, 'adminRoles']);
    });
    
    Route::middleware('role:Pegawai Gudang')->group(function () {
        Route::get('/dashboard/warehouse', [WebViewController::class, 'warehouseDashboard']);
        Route::get('/dashboard/warehouse/inventory', [WebViewController::class, 'warehouseInventory']);
    });
    
    Route::middleware('role:CS')->group(function () {
        Route::get('/dashboard/cs', [WebViewController::class, 'csDashboard']);
        Route::get('/dashboard/cs/customers', [WebViewController::class, 'csCustomers']);
    });
    
    Route::middleware('role:Penitip')->group(function () {
        Route::get('/dashboard/consignor', [WebViewController::class, 'consignorDashboard']);
        Route::get('/dashboard/consignor/items', [WebViewController::class, 'consignorItems']);
        Route::get('/dashboard/consignor/transactions', [WebViewController::class, 'consignorTransactions']);
    });
    
    Route::middleware('role:Pembeli')->group(function () {
        Route::get('/dashboard/buyer', [WebViewController::class, 'buyerDashboard']);
        Route::get('/dashboard/buyer/orders', [WebViewController::class, 'buyerOrders']);
        Route::get('/dashboard/buyer/profile', [WebViewController::class, 'buyerProfile']);
    });
    
    Route::middleware('role:Organisasi')->group(function () {
        Route::get('/dashboard/organization', [WebViewController::class, 'organizationDashboard']);
        Route::get('/dashboard/organization/donations', [WebViewController::class, 'organizationDonations']);
    });
    
    // Cart and checkout routes
    Route::get('/cart', [WebViewController::class, 'cart']);
    Route::get('/checkout', [WebViewController::class, 'checkout']);
    Route::get('/order-success', [WebViewController::class, 'orderSuccess']);
    
    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
    
    // Alamat routes
    Route::prefix('alamat')->group(function () {
        Route::get('/', [AlamatController::class, 'index']);
        Route::post('/', [AlamatController::class, 'store']);
        Route::get('/{id}', [AlamatController::class, 'show']);
        Route::put('/{id}', [AlamatController::class, 'update']);
        Route::delete('/{id}', [AlamatController::class, 'destroy']);
    });
    
    // Barang routes
    Route::prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index']);
        Route::post('/', [BarangController::class, 'store']);
        Route::get('/{id}', [BarangController::class, 'show']);
        Route::put('/{id}', [BarangController::class, 'update']);
        Route::delete('/{id}', [BarangController::class, 'destroy']);
    });
    
    // Detail Transaksi routes
    Route::prefix('detail-transaksi')->group(function () {
        Route::get('/', [DetailTransaksiController::class, 'index']);
        Route::post('/', [DetailTransaksiController::class, 'store']);
        Route::get('/{id}', [DetailTransaksiController::class, 'show']);
        Route::put('/{id}', [DetailTransaksiController::class, 'update']);
        Route::delete('/{id}', [DetailTransaksiController::class, 'destroy']);
    });
    
    // Diskusi Produk routes
    Route::prefix('diskusi-produk')->group(function () {
        Route::get('/', [DiskusiProdukController::class, 'index']);
        Route::post('/', [DiskusiProdukController::class, 'store']);
        Route::get('/{id}', [DiskusiProdukController::class, 'show']);
        Route::put('/{id}', [DiskusiProdukController::class, 'update']);
        Route::delete('/{id}', [DiskusiProdukController::class, 'destroy']);
    });
    
    // Donasi routes
    Route::prefix('donasi')->group(function () {
        Route::get('/', [DonasiController::class, 'index']);
        Route::post('/', [DonasiController::class, 'store']);
        Route::get('/{id}', [DonasiController::class, 'show']);
        Route::put('/{id}', [DonasiController::class, 'update']);
        Route::delete('/{id}', [DonasiController::class, 'destroy']);
    });
    
    // Garansi routes
    Route::prefix('garansi')->group(function () {
        Route::get('/', [GaransiController::class, 'index']);
        Route::post('/', [GaransiController::class, 'store']);
        Route::get('/{id}', [GaransiController::class, 'show']);
        Route::put('/{id}', [GaransiController::class, 'update']);
        Route::delete('/{id}', [GaransiController::class, 'destroy']);
    });
    
    // Kategori Barang routes
    Route::prefix('kategori-barang')->group(function () {
        Route::get('/', [KategoriBarangController::class, 'index']);
        Route::post('/', [KategoriBarangController::class, 'store']);
        Route::get('/{id}', [KategoriBarangController::class, 'show']);
        Route::put('/{id}', [KategoriBarangController::class, 'update']);
        Route::delete('/{id}', [KategoriBarangController::class, 'destroy']);
    });
    
    // Keranjang Belanja routes
    Route::prefix('keranjang-belanja')->group(function () {
        Route::get('/', [KeranjangBelanjaController::class, 'index']);
        Route::post('/', [KeranjangBelanjaController::class, 'store']);
        Route::get('/{id}', [KeranjangBelanjaController::class, 'show']);
        Route::put('/{id}', [KeranjangBelanjaController::class, 'update']);
        Route::delete('/{id}', [KeranjangBelanjaController::class, 'destroy']);
    });
    
    // Komisi routes
    Route::prefix('komisi')->group(function () {
        Route::get('/', [KomisiController::class, 'index']);
        Route::post('/', [KomisiController::class, 'store']);
        Route::get('/{id}', [KomisiController::class, 'show']);
        Route::put('/{id}', [KomisiController::class, 'update']);
        Route::delete('/{id}', [KomisiController::class, 'destroy']);
    });
    
    // Merch routes
    Route::prefix('merch')->group(function () {
        Route::get('/', [MerchController::class, 'index']);
        Route::post('/', [MerchController::class, 'store']);
        Route::get('/{id}', [MerchController::class, 'show']);
        Route::put('/{id}', [MerchController::class, 'update']);
        Route::delete('/{id}', [MerchController::class, 'destroy']);
    });
    
    // Organisasi routes
    Route::prefix('organisasi')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index']);
        Route::post('/', [OrganisasiController::class, 'store']);
        Route::get('/{id}', [OrganisasiController::class, 'show']);
        Route::put('/{id}', [OrganisasiController::class, 'update']);
        Route::delete('/{id}', [OrganisasiController::class, 'destroy']);
    });
    
    // Pegawai routes
    Route::prefix('pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store']);
        Route::get('/{id}', [PegawaiController::class, 'show']);
        Route::put('/{id}', [PegawaiController::class, 'update']);
        Route::delete('/{id}', [PegawaiController::class, 'destroy']);
    });
    
    // Pembeli routes
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index']);
        Route::post('/', [PembeliController::class, 'store']);
        Route::get('/{id}', [PembeliController::class, 'show']);
        Route::put('/{id}', [PembeliController::class, 'update']);
        Route::delete('/{id}', [PembeliController::class, 'destroy']);
    });
    
    // Pengiriman routes
    Route::prefix('pengiriman')->group(function () {
        Route::get('/', [PengirimanController::class, 'index']);
        Route::post('/', [PengirimanController::class, 'store']);
        Route::get('/{id}', [PengirimanController::class, 'show']);
        Route::put('/{id}', [PengirimanController::class, 'update']);
        Route::delete('/{id}', [PengirimanController::class, 'destroy']);
    });
    
    // Penitip routes
    Route::prefix('penitip')->group(function () {
        Route::get('/', [PenitipController::class, 'index']);
        Route::post('/', [PenitipController::class, 'store']);
        Route::get('/{id}', [PenitipController::class, 'show']);
        Route::put('/{id}', [PenitipController::class, 'update']);
        Route::delete('/{id}', [PenitipController::class, 'destroy']);
    });
    
    // Role routes
    Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
    });
    
    // Transaksi routes
    Route::prefix('transaksi')->group(function () {
        Route::get('/', [TransaksiController::class, 'index']);
        Route::post('/', [TransaksiController::class, 'store']);
        Route::get('/{id}', [TransaksiController::class, 'show']);
        Route::put('/{id}', [TransaksiController::class, 'update']);
        Route::delete('/{id}', [TransaksiController::class, 'destroy']);
    });
    
    // Transaksi Merch routes
    Route::prefix('transaksi-merch')->group(function () {
        Route::get('/', [TransaksiMerchController::class, 'index']);
        Route::post('/', [TransaksiMerchController::class, 'store']);
        Route::get('/{id}', [TransaksiMerchController::class, 'show']);
        Route::put('/{id}', [TransaksiMerchController::class, 'update']);
        Route::delete('/{id}', [TransaksiMerchController::class, 'destroy']);
    });
    
    // Transaksi Penitipan routes
    Route::prefix('transaksi-penitipan')->group(function () {
        Route::get('/', [TransaksiPenitipanController::class, 'index']);
        Route::post('/', [TransaksiPenitipanController::class, 'store']);
        Route::get('/{id}', [TransaksiPenitipanController::class, 'show']);
        Route::put('/{id}', [TransaksiPenitipanController::class, 'update']);
        Route::delete('/{id}', [TransaksiPenitipanController::class, 'destroy']);
    });
});
