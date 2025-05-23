<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\KategoriBarangController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\GaransiController;
use App\Http\Controllers\Api\KeranjangBelanjaController;
use App\Http\Controllers\Api\PenitipController;
use App\Http\Controllers\Api\PembeliController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\OrganisasiController;
use App\Http\Controllers\Api\WebViewController;
use App\Http\Controllers\Api\DashboardBuyerController;
use App\Http\Controllers\Api\BuyerTransactionController;
use App\Http\Controllers\Api\DashboardWarehouseController;
use App\Http\Controllers\Api\DashboardCSController;
use App\Http\Controllers\Api\DashboardHunterController;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\DiskusiProduk;

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

// Public Routes
Route::get('/', function () {
   return view('home');
})->name('home');

Route::get('/products', function () {
   return view('products.index');
})->name('products.index');

Route::get('/products/{id}', function ($id) {
   return view('products.show', ['id' => $id]);
})->name('products.show');

Route::get('/products/category/{category}', function ($category) {
   return view('products.index', ['category' => $category]);
})->name('products.category');

Route::get('/warranty/check', function () {
   return view('warranty.check');
})->name('warranty.check');

Route::get('/about', function () {
   return view('about');
})->name('about');

Route::get('/contact', function () {
   return view('errors.missing-view', ['view' => 'contact']);
})->name('contact');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');

// Protected Routes - Main Dashboard Redirect
Route::middleware(['auth'])->get('/dashboard', function () {
   $user = Auth::user();
   $role = strtolower($user->role->nama_role ?? '');

   return match ($role) {
       'owner' => redirect()->route('dashboard.owner'),
       'admin' => redirect()->route('dashboard.admin'),
       'pegawai', 'gudang' => redirect()->route('dashboard.warehouse.index'), // PERBAIKAN: Menggunakan nama route yang benar
       'cs' => redirect()->route('dashboard.cs'),
       'penitip', 'penjual' => redirect()->route('dashboard.consignor'),
       'organisasi' => redirect()->route('dashboard.organization'),
       'pembeli' => redirect()->route('dashboard.buyer'),
       'hunter' => redirect()->route('dashboard.hunter'),
       default => abort(403, 'Role tidak dikenali')
   };
})->name('dashboard');

// Role-specific Dashboard Routes
Route::middleware(['auth', 'role:pembeli'])->get('/dashboard/buyer', [DashboardBuyerController::class, 'index'])->name('dashboard.buyer');

Route::middleware(['auth', 'role:cs'])->get('/dashboard/cs', [DashboardCSController::class, 'index'])->name('dashboard.cs');

// PERBAIKAN: Menggunakan nama route yang konsisten
Route::middleware(['auth', 'role:gudang'])->get('/dashboard/warehouse', [DashboardWarehouseController::class, 'index'])->name('dashboard.warehouse.index');

// Hunter Dashboard Routes
Route::middleware(['auth', 'role:hunter'])->prefix('dashboard/hunter')->name('dashboard.hunter')->group(function () {
    Route::get('/', [DashboardHunterController::class, 'index']);
    Route::get('/komisi', [DashboardHunterController::class, 'komisi'])->name('.komisi');
    Route::get('/riwayat-penjemputan', [DashboardHunterController::class, 'riwayatPenjemputan'])->name('.riwayat-penjemputan');
    Route::get('/detail-penjemputan/{id}', [DashboardHunterController::class, 'detailPenjemputan'])->name('.detail-penjemputan');
    Route::put('/update-status-penjemputan/{id}', [DashboardHunterController::class, 'updateStatusPenjemputan'])->name('.update-status-penjemputan');
});

Route::get('/dashboard/owner', function () {
   return view('dashboard.owner.index');
})->name('dashboard.owner');

Route::get('/dashboard/admin', function () {
   return view('dashboard.admin.index');
})->name('dashboard.admin');

Route::get('/dashboard/consignor', function () {
   return view('dashboard.consignor.index');
})->name('dashboard.consignor');

Route::get('/dashboard/organization', function () {
   return view('dashboard.organization.index');
})->name('dashboard.organization');

// Profile Routes
Route::middleware(['auth'])->group(function () {
   Route::get('/dashboard/profil', function () {
       return view('errors.missing-view', ['view' => 'dashboard.profile.show']);
   })->name('profile.show');
   
   Route::put('/dashboard/profil', [UserController::class, 'update'])->name('profile.update');
});

// Buyer Routes
Route::middleware(['auth', 'role:pembeli'])->group(function () {
   // Transaction Routes
   Route::get('/dashboard/buyer/transactions', [BuyerTransactionController::class, 'index'])->name('buyer.transactions');
   Route::get('/dashboard/buyer/transactions/{id}', [BuyerTransactionController::class, 'show'])->name('buyer.transactions.show');
   
   // Cart Routes
   Route::get('/dashboard/keranjang', function () {
       return view('errors.missing-view', ['view' => 'dashboard.buyer.cart.index']);
   })->name('cart.index');
   
   Route::post('/dashboard/keranjang/add', [KeranjangBelanjaController::class, 'store'])->name('cart.add');
   Route::put('/dashboard/keranjang/update', [KeranjangBelanjaController::class, 'update'])->name('cart.update');
   Route::delete('/dashboard/keranjang/remove/{id}', [KeranjangBelanjaController::class, 'destroy'])->name('cart.remove');
   
   // Checkout Routes
   Route::get('/checkout', function () {
       return view('errors.missing-view', ['view' => 'dashboard.buyer.checkout.index']);
   })->name('checkout.index');
   
   Route::post('/checkout/process', [TransaksiController::class, 'store'])->name('checkout.process');
});

// Consignor Routes
Route::middleware(['auth', 'role:penitip'])->group(function () {
   // My Items Routes
   Route::get('/dashboard/barang-saya', function () {
       return view('errors.missing-view', ['view' => 'dashboard.consignor.items.index']);
   })->name('consignor.items');
   
   Route::get('/dashboard/barang-saya/create', function () {
       return view('errors.missing-view', ['view' => 'dashboard.consignor.items.create']);
   })->name('consignor.items.create');
   
   Route::post('/dashboard/barang-saya', [BarangController::class, 'store'])->name('consignor.items.store');
   
   Route::get('/dashboard/barang-saya/{id}/edit', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.consignor.items.edit', 'id' => $id]);
   })->name('consignor.items.edit');
   
   Route::put('/dashboard/barang-saya/{id}', [BarangController::class, 'update'])->name('consignor.items.update');
   Route::delete('/dashboard/barang-saya/{id}', [BarangController::class, 'destroy'])->name('consignor.items.destroy');
   
   // Consignment Transaction Routes
   Route::get('/dashboard/transaksi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.index']);
   })->name('consignor.transactions');
   
   Route::get('/dashboard/transaksi/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.show', 'id' => $id]);
   })->name('consignor.transactions.show');
});

// Customer Service Routes
Route::middleware(['auth', 'role:cs'])->group(function () {
   // Discussions Routes
   Route::get('/dashboard/cs/diskusi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.cs.discussions.index']);
   })->name('dashboard.cs.discussions');
   
   // Consignor Management Routes
   Route::get('/dashboard/penitip', function () {
       return view('errors.missing-view', ['view' => 'dashboard.cs.consignors.index']);
   })->name('cs.consignors');
   
   Route::get('/dashboard/penitip/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.cs.consignors.show', 'id' => $id]);
   })->name('cs.consignors.show');
   
   // Product Discussion Routes
   Route::get('/dashboard/diskusi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.cs.discussions.index']);
   })->name('cs.discussions');
   
   Route::get('/dashboard/diskusi/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.cs.discussions.show', 'id' => $id]);
   })->name('cs.discussions.show');
   
   // Payment Verification Routes
   Route::get('/dashboard/verifikasi-pembayaran', function () {
       return view('errors.missing-view', ['view' => 'dashboard.cs.payment_verifications.index']);
   })->name('cs.payment.verifications');
   
   Route::get('/dashboard/verifikasi-pembayaran/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.cs.payment_verifications.show', 'id' => $id]);
   })->name('cs.payment.verifications.show');
});

// Warehouse Staff Routes
Route::middleware(['auth', 'role:gudang'])->group(function () {
   // Warehouse Dashboard Routes
   Route::prefix('dashboard/warehouse')->name('dashboard.warehouse.')->group(function () {
       Route::get('/', [DashboardWarehouseController::class, 'index'])->name('index');
       Route::get('/inventory', [DashboardWarehouseController::class, 'inventory'])->name('inventory');
       Route::get('/shipments', [DashboardWarehouseController::class, 'shipments'])->name('shipments');
       Route::get('/shipments/{id}', [DashboardWarehouseController::class, 'showShipment'])->name('shipment.show');
       Route::put('/shipments/{id}/update-status', [DashboardWarehouseController::class, 'updateShipmentStatus'])->name('shipment.update-status');
       Route::get('/items/{id}', [DashboardWarehouseController::class, 'showItem'])->name('item.show');
       Route::put('/items/{id}/update-status', [DashboardWarehouseController::class, 'updateItemStatus'])->name('item.update-status');
   });
   
   // Legacy Routes (for backward compatibility)
   Route::get('/dashboard/barang-titipan', function () {
       return redirect()->route('dashboard.warehouse.inventory');
   })->name('warehouse.items');
   
   Route::get('/dashboard/barang-titipan/{id}', function ($id) {
       return redirect()->route('dashboard.warehouse.item.show', $id);
   })->name('warehouse.items.show');
   
   Route::get('/dashboard/pengiriman', function () {
       return redirect()->route('dashboard.warehouse.shipments');
   })->name('warehouse.shipments');
   
   Route::get('/dashboard/pengiriman/{id}', function ($id) {
       return redirect()->route('dashboard.warehouse.shipment.show', $id);
   })->name('warehouse.shipments.show');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
   // Employee Management Routes
   Route::get('/dashboard/pegawai', function () {
       return view('errors.missing-view', ['view' => 'dashboard.admin.employees.index']);
   })->name('admin.employees');
   
   Route::get('/dashboard/pegawai/create', function () {
       return view('errors.missing-view', ['view' => 'dashboard.admin.employees.create']);
   })->name('admin.employees.create');
   
   Route::post('/dashboard/pegawai', [PegawaiController::class, 'store'])->name('admin.employees.store');
   
   Route::get('/dashboard/pegawai/{id}/edit', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.admin.employees.edit', 'id' => $id]);
   })->name('admin.employees.edit');
   
   Route::put('/dashboard/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.employees.update');
   Route::delete('/dashboard/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.employees.destroy');
   
   // Organization Management Routes
   Route::get('/dashboard/organisasi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.admin.organizations.index']);
   })->name('admin.organizations');
   
   Route::get('/dashboard/organisasi/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.admin.organizations.show', 'id' => $id]);
   })->name('admin.organizations.show');
   
   // Employee Verification Routes
   Route::get('/dashboard/verifikasi-pegawai', function () {
       return view('errors.missing-view', ['view' => 'dashboard.admin.employee_verifications.index']);
   })->name('admin.employee.verifications');
   
   Route::get('/dashboard/verifikasi-pegawai/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.admin.employee_verifications.show', 'id' => $id]);
   })->name('admin.employee.verifications.show');
   
   Route::put('/dashboard/verifikasi-pegawai/{id}/approve', [PegawaiController::class, 'approve'])->name('admin.employee.approve');
   Route::put('/dashboard/verifikasi-pegawai/{id}/reject', [PegawaiController::class, 'reject'])->name('admin.employee.reject');
});

// Owner Routes
Route::middleware(['auth', 'role:owner'])->group(function () {
   // Donation Routes
   Route::get('/dashboard/donasi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.owner.donations.index']);
   })->name('owner.donations');

   Route::get('/dashboard/donasi/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.owner.donations.show', 'id' => $id]);
   })->name('owner.donations.show');

   // Report Routes
   Route::get('/dashboard/laporan/penjualan', function () {
       return view('errors.missing-view', ['view' => 'dashboard.owner.reports.sales']);
   })->name('owner.reports.sales');

   Route::get('/dashboard/laporan/komisi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.owner.reports.commission']);
   })->name('owner.reports.commission');

   Route::get('/dashboard/laporan/stok', function () {
       return view('errors.missing-view', ['view' => 'dashboard.owner.reports.stock']);
   })->name('owner.reports.stock');

   Route::get('/dashboard/laporan/kategori', function () {
       return view('errors.missing-view', ['view' => 'dashboard.owner.reports.category']);
   })->name('owner.reports.category');
});

// Organization Routes
Route::middleware(['auth', 'role:organisasi'])->group(function () {
   // Donation Request Routes
   Route::get('/dashboard/request-donasi', function () {
       return view('errors.missing-view', ['view' => 'dashboard.organization.donation_requests.index']);
   })->name('organization.donation.requests');
   
   Route::get('/dashboard/request-donasi/create', function () {
       return view('errors.missing-view', ['view' => 'dashboard.organization.donation_requests.create']);
   })->name('organization.donation.requests.create');
   
   Route::post('/dashboard/request-donasi', [DonasiController::class, 'store'])->name('organization.donation.requests.store');
   
   Route::get('/dashboard/request-donasi/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.organization.donation_requests.show', 'id' => $id]);
   })->name('organization.donation.requests.show');
   
   // Received Donations Routes
   Route::get('/dashboard/donasi-diterima', function () {
       return view('errors.missing-view', ['view' => 'dashboard.organization.received_donations.index']);
   })->name('organization.received.donations');
   
   Route::get('/dashboard/donasi-diterima/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.organization.received_donations.show', 'id' => $id]);
   })->name('organization.received.donations.show');
});

// Kurir Routes
Route::middleware(['auth', 'role:kurir'])->group(function () {
   // Delivery Routes
   Route::get('/dashboard/pengiriman-kurir', function () {
       return view('errors.missing-view', ['view' => 'dashboard.courier.deliveries.index']);
   })->name('courier.deliveries');
   
   Route::get('/dashboard/pengiriman-kurir/{id}', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.courier.deliveries.show', 'id' => $id]);
   })->name('courier.deliveries.show');
   
   Route::put('/dashboard/pengiriman-kurir/{id}/update-status', function ($id) {
       return view('errors.missing-view', ['view' => 'dashboard.courier.deliveries.update_status', 'id' => $id]);
   })->name('courier.deliveries.update-status');
});

// Hunter Routes - Menggunakan route group yang sudah ada di atas
Route::middleware(['auth', 'role:hunter'])->group(function () {
   // Item Collection Routes - Ini adalah route legacy yang akan diarahkan ke route baru
   Route::get('/dashboard/pengambilan-barang', function () {
       return redirect()->route('dashboard.hunter.riwayat-penjemputan');
   })->name('hunter.collections');
   
   Route::get('/dashboard/pengambilan-barang/{id}', function ($id) {
       return redirect()->route('dashboard.hunter.detail-penjemputan', $id);
   })->name('hunter.collections.show');
   
   Route::put('/dashboard/pengambilan-barang/{id}/update-status', function ($id) {
       return redirect()->route('dashboard.hunter.update-status-penjemputan', $id);
   })->name('hunter.collections.update-status');
});
