<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\DashboardOrganisasiController;
use App\Http\Controllers\Api\DashboardAdminController;
use App\Http\Controllers\Api\DashboardConsignorController;
use App\Http\Controllers\Api\BuyerProfileController;
use App\Http\Controllers\Api\DashboardProfileController;

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

// ========================================
// PUBLIC ROUTES
// ========================================

Route::get('/', [WebViewController::class, 'home'])->name('home');

// Products Routes
Route::get('/products', [WebViewController::class, 'products'])->name('products.index');
Route::get('/products/{id}', [WebViewController::class, 'productDetail'])->name('products.show');

// Alias for backward compatibility
Route::redirect('/products-alias', '/products')->name('products');

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

// Product discussion route (for the product detail page)
Route::post('/products/{id}/discussion', function() {
    return redirect()->back()->with('success', 'Pertanyaan berhasil dikirim.');
})->name('product.discussion.store');

// ========================================
// AUTHENTICATION ROUTES
// ========================================

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

// ========================================
// CART ROUTES (AUTHENTICATED USERS)
// ========================================

Route::middleware(['auth:web'])->group(function () {
    // Public cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [KeranjangBelanjaController::class, 'viewCart'])->name('index');
        Route::post('/add', [KeranjangBelanjaController::class, 'store'])->name('add');
        Route::put('/{id}', [KeranjangBelanjaController::class, 'update'])->name('update');
        Route::delete('/{id}', [KeranjangBelanjaController::class, 'destroy'])->name('remove');
        Route::delete('/clear/all', [KeranjangBelanjaController::class, 'clearCart'])->name('clear');
        Route::get('/count', [KeranjangBelanjaController::class, 'getCartCount'])->name('count');
    });

    // Debug routes (development only)
    if (config('app.debug')) {
        Route::get('/cart/debug', [KeranjangBelanjaController::class, 'debug'])->name('cart.debug');
        Route::get('/cart/test-add', [KeranjangBelanjaController::class, 'testAdd'])->name('cart.test-add');
    }
});

// ========================================
// PROTECTED ROUTES - MAIN DASHBOARD REDIRECT
// ========================================

Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();
    $role = strtolower($user->role->nama_role ?? '');

    return match ($role) {
        'owner' => redirect()->route('dashboard.owner'),
        'admin' => redirect()->route('dashboard.admin.index'),
        'pegawai', 'gudang', 'pegawai gudang' => redirect()->route('dashboard.warehouse.index'),
        'cs' => redirect()->route('dashboard.cs'),
        'penitip', 'penjual' => redirect()->route('dashboard.consignor'),
        'organisasi' => redirect()->route('dashboard.organization'),
        'pembeli' => redirect()->route('dashboard.buyer'),
        'hunter' => redirect()->route('dashboard.hunter'),
        default => abort(403, 'Role tidak dikenali: ' . $role)
    };
})->name('dashboard');

// ========================================
// PROFILE ROUTES (ALL AUTHENTICATED USERS)
// ========================================

Route::middleware(['auth'])->group(function () {
    // Main profile routes
    Route::get('/dashboard/profil', function () {
        return view('errors.missing-view', ['view' => 'dashboard.profile.show']);
    })->name('profile.show');
    
    Route::put('/dashboard/profil', [UserController::class, 'update'])->name('profile.update');
    
    // Alternative profile routes
    Route::get('/profile', [WebViewController::class, 'profilePembeli'])->name('profile.pembeli.show');
    Route::put('/profile', [WebViewController::class, 'updateProfilePembeli'])->name('profile.pembeli.update');
    
    // Dashboard profile routes
    Route::get('/dashboard/profile', function () {
        return view('errors.missing-view', ['view' => 'dashboard.profile.show']);
    })->name('dashboard.profile.show');
    
    Route::put('/dashboard/profile', [UserController::class, 'update'])->name('dashboard.profile.update');
});

// ========================================
// BUYER ROUTES
// ========================================

Route::middleware(['auth', 'role:pembeli'])->group(function () {
    // Dashboard
    Route::get('/dashboard/buyer', [DashboardBuyerController::class, 'index'])->name('dashboard.buyer');
    
    // Transaction Routes
    Route::get('/dashboard/buyer/transactions', [BuyerTransactionController::class, 'index'])->name('buyer.transactions');
    Route::get('/dashboard/buyer/transactions/{id}', [BuyerTransactionController::class, 'show'])->name('buyer.transactions.show');
    
    // Cart Routes (dashboard version)
    Route::get('/dashboard/keranjang', [KeranjangBelanjaController::class, 'viewCart'])->name('buyer.cart');
    Route::post('/dashboard/keranjang/add', [KeranjangBelanjaController::class, 'store'])->name('buyer.cart.add');
    Route::put('/dashboard/keranjang/update', [KeranjangBelanjaController::class, 'update'])->name('buyer.cart.update');
    Route::delete('/dashboard/keranjang/remove/{id}', [KeranjangBelanjaController::class, 'destroy'])->name('buyer.cart.remove');

    Route::post('/dashboard/keranjang/alt/add', [KeranjangBelanjaController::class, 'store'])->name('cart.add');
    Route::put('/dashboard/keranjang/alt/update', [KeranjangBelanjaController::class, 'update'])->name('cart.update');
    Route::delete('/dashboard/keranjang/alt/remove/{id}', [KeranjangBelanjaController::class, 'destroy'])->name('cart.remove');
    
    // Alamat Routes
    Route::get('/dashboard/alamat', [WebViewController::class, 'alamatIndex'])->name('buyer.alamat.index');
    Route::get('/dashboard/alamat/create', [WebViewController::class, 'alamatCreate'])->name('buyer.alamat.create');
    Route::post('/dashboard/alamat', [WebViewController::class, 'alamatStore'])->name('buyer.alamat.store');
    Route::get('/dashboard/alamat/{id}/edit', [WebViewController::class, 'alamatEdit'])->name('buyer.alamat.edit');
    Route::put('/dashboard/alamat/{id}', [WebViewController::class, 'alamatUpdate'])->name('buyer.alamat.update');
    Route::delete('/dashboard/alamat/{id}', [WebViewController::class, 'alamatDestroy'])->name('buyer.alamat.destroy');
    Route::patch('/dashboard/alamat/{id}/set-default', [WebViewController::class, 'alamatSetDefault'])->name('buyer.alamat.set-default');

    // Profile Routes
    Route::prefix('dashboard/buyer/profile')->name('buyer.profile.')->group(function () {
        Route::get('/', [BuyerProfileController::class, 'index'])->name('index');
        Route::put('/update', [BuyerProfileController::class, 'updateProfile'])->name('update');
        Route::get('/rewards', [BuyerProfileController::class, 'showRewardPoints'])->name('rewards');
        Route::get('/transaction-history', [BuyerProfileController::class, 'showTransactionHistory'])->name('transaction-history');
        Route::get('/transaction/{id}', [BuyerProfileController::class, 'showTransactionDetail'])->name('transaction-detail');
    });
    
    // Alternative profile routes for backward compatibility
    Route::prefix('buyer/profile')->name('buyer.profile.alt.')->group(function () {
        Route::get('/', [BuyerProfileController::class, 'index'])->name('index');
        Route::put('/update', [BuyerProfileController::class, 'updateProfile'])->name('update');
        Route::get('/rewards', [BuyerProfileController::class, 'showRewardPoints'])->name('rewards');
        Route::get('/transaction-history', [BuyerProfileController::class, 'showTransactionHistory'])->name('transaction-history');
        Route::get('/transaction/{id}', [BuyerProfileController::class, 'showTransactionDetail'])->name('transaction-detail');
    });
    
    // Checkout Routes
    Route::get('/checkout', function () {
        return view('errors.missing-view', ['view' => 'dashboard.buyer.checkout.index']);
    })->name('checkout.index');
    
    Route::post('/checkout/process', [TransaksiController::class, 'store'])->name('checkout.process');
});

// ========================================
// CONSIGNOR/PENITIP ROUTES
// ========================================

Route::middleware(['auth', 'role:penitip'])->group(function () {
    // Dashboard
    Route::get('/dashboard/consignor', [DashboardConsignorController::class, 'index'])->name('dashboard.consignor');
    
    // My Items Routes
    Route::get('/dashboard/barang-saya', [DashboardConsignorController::class, 'items'])->name('consignor.items');
    Route::get('/dashboard/barang-saya/create', [DashboardConsignorController::class, 'createItem'])->name('consignor.items.create');
    Route::post('/dashboard/barang-saya', [DashboardConsignorController::class, 'storeItem'])->name('consignor.items.store');
    Route::get('/dashboard/barang-saya/{id}', [DashboardConsignorController::class, 'showItem'])->name('consignor.items.show');
    Route::get('/dashboard/barang-saya/{id}/edit', [DashboardConsignorController::class, 'editItem'])->name('consignor.items.edit');
    Route::put('/dashboard/barang-saya/{id}', [DashboardConsignorController::class, 'updateItem'])->name('consignor.items.update');
    Route::delete('/dashboard/barang-saya/{id}', [DashboardConsignorController::class, 'destroyItem'])->name('consignor.items.destroy');
    
    // Consignment Transaction Routes - Enhanced
    Route::get('/dashboard/transaksi', [DashboardConsignorController::class, 'transactions'])->name('consignor.transactions');
    Route::get('/dashboard/transaksi/{id}', [DashboardConsignorController::class, 'showTransaction'])->name('consignor.transactions.show');
    
    // Extension Route - NEW
    Route::post('/dashboard/transaksi/extend', [DashboardConsignorController::class, 'extendTransaction'])->name('consignor.transactions.extend');
    
    // Fallback routes for missing views
    Route::get('/dashboard/transaksi/fallback', function () {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.index']);
    })->name('consignor.transactions.fallback');
    
    Route::get('/dashboard/transaksi/fallback/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.show', 'id' => $id]);
    })->name('consignor.transactions.show.fallback');
});

// ========================================
// WAREHOUSE STAFF ROUTES (ENHANCED)
// ========================================

Route::middleware(['auth', 'role:gudang,pegawai gudang'])->group(function () {
    // Warehouse Dashboard Routes
    Route::prefix('dashboard/warehouse')->name('dashboard.warehouse.')->group(function () {
        Route::get('/', [DashboardWarehouseController::class, 'index'])->name('index');
        Route::get('/inventory', [DashboardWarehouseController::class, 'inventory'])->name('inventory');
        Route::get('/transactions', [DashboardWarehouseController::class, 'transactionsList'])->name('transactions');
        Route::get('/shipments', [DashboardWarehouseController::class, 'shipments'])->name('shipments');
        
        // Consignment management
        Route::get('/consignment/create', [DashboardWarehouseController::class, 'createConsignmentItem'])->name('consignment.create');
        Route::post('/consignment', [DashboardWarehouseController::class, 'storeConsignmentItem'])->name('consignment.store');
        
        // Enhanced Item management with full editing capability
        Route::get('/items/{id}', [DashboardWarehouseController::class, 'showItem'])->name('item.show');
        Route::get('/items/{id}/edit', [DashboardWarehouseController::class, 'editItem'])->name('item.edit');
        Route::put('/items/{id}', [DashboardWarehouseController::class, 'updateItem'])->name('item.update');
        Route::put('/items/{id}/update-status', [DashboardWarehouseController::class, 'updateItemStatus'])->name('item.update-status');
        
        // Alternative item routes for compatibility
        Route::get('/item/{id}', [DashboardWarehouseController::class, 'showItem'])->name('item.show.alt');
        Route::put('/item/{id}/status', [DashboardWarehouseController::class, 'updateItemStatus'])->name('item.update-status.alt');
        
        // Consignment Transactions with search functionality
        Route::get('/consignment/transactions', [DashboardWarehouseController::class, 'consignmentTransactions'])->name('consignment.transactions');
        Route::get('/consignment/transaction/{id}', [DashboardWarehouseController::class, 'showConsignmentTransaction'])->name('consignment.transaction.show');
        
        // Shipment management
        Route::get('/shipment/{id}', [DashboardWarehouseController::class, 'showShipment'])->name('shipment.show');
        Route::put('/shipment/{id}/status', [DashboardWarehouseController::class, 'updateShipmentStatus'])->name('shipment.update-status');
        
        // Transaction management
        Route::post('/transaction/{id}/shipping', [DashboardWarehouseController::class, 'createShippingSchedule'])->name('create-shipping');
        Route::post('/transaction/{id}/pickup', [DashboardWarehouseController::class, 'createPickupSchedule'])->name('create-pickup');
        Route::get('/transaction/{id}/sales-note', [DashboardWarehouseController::class, 'generateSalesNote'])->name('sales-note');
        Route::post('/transaction/{id}/confirm', [DashboardWarehouseController::class, 'confirmItemReceived'])->name('confirm-received');
        Route::post('/transaction/{id}/status', [DashboardWarehouseController::class, 'updateTransactionStatus'])->name('update-transaction-status');
    });
    
    // Legacy Routes (for backward compatibility)
    Route::get('/dashboard/barang-titipan', function () {
        return redirect()->route('dashboard.warehouse.inventory');
    })->name('warehouse.items');
    
    Route::get('/dashboard/barang-titipan/{id}', function ($id) {
        return redirect()->route('dashboard.warehouse.item.show', $id);
    })->name('warehouse.items.show');
});

// ========================================
// CUSTOMER SERVICE ROUTES
// ========================================

Route::middleware(['auth', 'role:cs'])->group(function () {
    // Dashboard
    Route::get('/dashboard/cs', [DashboardCSController::class, 'index'])->name('dashboard.cs');
    
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

// ========================================
// ADMIN ROUTES
// ========================================

Route::middleware(['auth', 'role:admin'])->prefix('dashboard/admin')->name('dashboard.admin.')->group(function () {
    // Dashboard utama
    Route::get('/', [DashboardAdminController::class, 'index'])->name('index');
    
    // User management
    Route::get('/users', [DashboardAdminController::class, 'users'])->name('users');
    
    // Penitip management
    Route::get('/penitips', [DashboardAdminController::class, 'penitips'])->name('penitips');
    Route::get('/penitips/create', [DashboardAdminController::class, 'createPenitip'])->name('penitips.create');
    Route::post('/penitips', [DashboardAdminController::class, 'storePenitip'])->name('penitips.store');
    Route::get('/penitips/{id}', [DashboardAdminController::class, 'showPenitip'])->name('penitips.show');
    Route::get('/penitips/{id}/edit', [DashboardAdminController::class, 'editPenitip'])->name('penitips.edit');
    Route::put('/penitips/{id}', [DashboardAdminController::class, 'updatePenitip'])->name('penitips.update');
    Route::delete('/penitips/{id}', [DashboardAdminController::class, 'destroyPenitip'])->name('penitips.destroy');
    
    // Role management
    Route::get('/roles', [DashboardAdminController::class, 'roles'])->name('roles');
    
    // Employee management
    Route::get('/employees', [DashboardAdminController::class, 'employees'])->name('employees');
    Route::get('/employees/create', function () {
        return view('errors.missing-view', ['view' => 'dashboard.admin.employees.create']);
    })->name('employees.create');
    Route::post('/employees', [PegawaiController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}/edit', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.admin.employees.edit', 'id' => $id]);
    })->name('employees.edit');
    Route::put('/employees/{id}', [PegawaiController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [PegawaiController::class, 'destroy'])->name('employees.destroy');
    
    // Organization management
    Route::get('/organizations', [DashboardAdminController::class, 'organizations'])->name('organizations');
    Route::get('/organizations/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.admin.organizations.show', 'id' => $id]);
    })->name('organizations.show');
    
    // Employee Verification Routes
    Route::get('/employee-verifications', function () {
        return view('errors.missing-view', ['view' => 'dashboard.admin.employee_verifications.index']);
    })->name('employee.verifications');
    
    Route::get('/employee-verifications/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.admin.employee_verifications.show', 'id' => $id]);
    })->name('employee.verifications.show');
    
    Route::put('/employee-verifications/{id}/approve', [PegawaiController::class, 'approve'])->name('employee.approve');
    Route::put('/employee-verifications/{id}/reject', [PegawaiController::class, 'reject'])->name('employee.reject');
});

// Alternative admin route for compatibility
Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');

// ========================================
// OWNER ROUTES
// ========================================

Route::middleware(['auth', 'role:owner'])->group(function () {
    // Dashboard
    Route::get('/dashboard/owner', function () {
        return view('dashboard.owner.index');
    })->name('dashboard.owner');
    
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

// ========================================
// ORGANIZATION ROUTES
// ========================================

Route::middleware(['auth', 'role:organisasi'])->group(function () {
    // Dashboard
    Route::get('/dashboard/organization', [DashboardOrganisasiController::class, 'index'])->name('dashboard.organization');
    
    // Donations
    Route::get('/dashboard/organization/donations', [DashboardOrganisasiController::class, 'donations'])->name('dashboard.organization.donations');
    Route::get('/dashboard/organization/donations/{id}', [DashboardOrganisasiController::class, 'showDonation'])->name('dashboard.organization.donations.show');
    
    // Donation Requests
    Route::get('/dashboard/organization/requests', [DashboardOrganisasiController::class, 'requests'])->name('dashboard.organization.requests');
    Route::get('/dashboard/organization/requests/create', [DashboardOrganisasiController::class, 'createRequest'])->name('dashboard.organization.requests.create');
    Route::post('/dashboard/organization/requests', [DashboardOrganisasiController::class, 'storeRequest'])->name('dashboard.organization.requests.store');
    Route::get('/dashboard/organization/requests/{id}', [DashboardOrganisasiController::class, 'showRequest'])->name('dashboard.organization.requests.show');
    Route::get('/dashboard/organization/requests/{id}/edit', [DashboardOrganisasiController::class, 'editRequest'])->name('dashboard.organization.requests.edit');
    Route::put('/dashboard/organization/requests/{id}', [DashboardOrganisasiController::class, 'updateRequest'])->name('dashboard.organization.requests.update');
    
    // Profile
    Route::get('/dashboard/organization/profile', [DashboardOrganisasiController::class, 'profile'])->name('dashboard.organization.profile');
    Route::put('/dashboard/organization/profile', [DashboardOrganisasiController::class, 'updateProfile'])->name('dashboard.organization.profile.update');
    
    // Reports
    Route::get('/dashboard/organization/reports', [DashboardOrganisasiController::class, 'reports'])->name('dashboard.organization.reports');
    
    // Legacy routes for backward compatibility
    Route::get('/dashboard/request-donasi', function () {
        return redirect()->route('dashboard.organization.requests');
    })->name('organization.donation.requests');
    
    Route::get('/dashboard/request-donasi/create', function () {
        return redirect()->route('dashboard.organization.requests.create');
    })->name('organization.donation.requests.create');
    
    Route::post('/dashboard/request-donasi', [DonasiController::class, 'store'])->name('organization.donation.requests.store');
    
    Route::get('/dashboard/request-donasi/{id}', function ($id) {
        return redirect()->route('dashboard.organization.requests.show', $id);
    })->name('organization.donation.requests.show');
    
    Route::get('/dashboard/donasi-diterima', function () {
        return redirect()->route('dashboard.organization.donations');
    })->name('organization.received.donations');
    
    Route::get('/dashboard/donasi-diterima/{id}', function ($id) {
        return redirect()->route('dashboard.organization.donations.show', $id);
    })->name('organization.received.donations.show');
    
    // Alternative organization dashboard route
    Route::get('/dashboard/organization/alt', function () {
        return view('dashboard.organization.index');
    })->name('dashboard.organization.alt');
});

// ========================================
// HUNTER ROUTES
// ========================================

Route::middleware(['auth', 'role:hunter'])->prefix('dashboard/hunter')->name('dashboard.hunter')->group(function () {
    Route::get('/', [DashboardHunterController::class, 'index']);
    Route::get('/komisi', [DashboardHunterController::class, 'komisi'])->name('.komisi');
    Route::get('/riwayat-penjemputan', [DashboardHunterController::class, 'riwayatPenjemputan'])->name('.riwayat-penjemputan');
    Route::get('/detail-penjemputan/{id}', [DashboardHunterController::class, 'detailPenjemputan'])->name('.detail-penjemputan');
    Route::put('/update-status-penjemputan/{id}', [DashboardHunterController::class, 'updateStatusPenjemputan'])->name('.update-status-penjemputan');
});

// Hunter Legacy Routes (for backward compatibility)
Route::middleware(['auth', 'role:hunter'])->group(function () {
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

// ========================================
// COURIER ROUTES
// ========================================

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

// ========================================
// DEBUG ROUTES (DEVELOPMENT ONLY)
// ========================================

if (config('app.debug')) {
    Route::middleware(['auth:web'])->group(function () {
        // Basic cart debug
        Route::get('/debug-cart', function() {
            $user = Auth::guard('web')->user();
            $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            $cartItems = \App\Models\KeranjangBelanja::with(['barang', 'barang.kategoriBarang'])
                ->where('pembeli_id', $pembeliId)
                ->get();
            
            return response()->json([
                'user' => $user,
                'pembeli' => $pembeli,
                'pembeli_id' => $pembeliId,
                'cart_items' => $cartItems,
                'cart_count' => $cartItems->count(),
                'table_structure' => DB::select("DESCRIBE keranjang_belanja")
            ]);
        });
        
        // Detailed cart debug
        Route::get('/debug-cart-detailed', function() {
            $user = Auth::guard('web')->user();
            $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            // Check table structure
            $tableStructure = DB::select("DESCRIBE keranjang_belanja");
            
            // Get all cart items with different approaches
            $allCartItems = \App\Models\KeranjangBelanja::all();
            $userCartItems = \App\Models\KeranjangBelanja::where('pembeli_id', $user->id)->get();
            $pembeliCartItems = \App\Models\KeranjangBelanja::where('pembeli_id', $pembeliId)->get();
            
            // Raw queries
            $rawUserItems = DB::table('keranjang_belanja')->where('pembeli_id', $user->id)->get();
            $rawPembeliItems = DB::table('keranjang_belanja')->where('pembeli_id', $pembeliId)->get();
            
            // Check barang table
            $allBarang = \App\Models\Barang::limit(5)->get();
            
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role->nama_role ?? 'No Role'
                ],
                'pembeli' => $pembeli ? $pembeli->toArray() : null,
                'pembeli_id_used' => $pembeliId,
                'table_structure' => $tableStructure,
                'cart_queries' => [
                    'all_cart_items' => $allCartItems->toArray(),
                    'user_cart_items' => $userCartItems->toArray(),
                    'pembeli_cart_items' => $pembeliCartItems->toArray(),
                    'raw_user_items' => $rawUserItems->toArray(),
                    'raw_pembeli_items' => $rawPembeliItems->toArray()
                ],
                'sample_barang' => $allBarang->toArray(),
                'counts' => [
                    'all_cart' => $allCartItems->count(),
                    'user_cart' => $userCartItems->count(),
                    'pembeli_cart' => $pembeliCartItems->count(),
                    'raw_user' => $rawUserItems->count(),
                    'raw_pembeli' => $rawPembeliItems->count()
                ]
            ]);
        });
        
        // Test relationships
        Route::get('/debug-relationships', function() {
            $cartItem = \App\Models\KeranjangBelanja::first();
            
            if (!$cartItem) {
                return response()->json(['error' => 'No cart items found']);
            }
            
            return response()->json([
                'cart_item' => $cartItem->toArray(),
                'barang_relationship' => $cartItem->barang ? $cartItem->barang->toArray() : null,
                'kategori_relationship' => $cartItem->barang && $cartItem->barang->kategoriBarang ? 
                    $cartItem->barang->kategoriBarang->toArray() : null,
                'pembeli_relationship' => $cartItem->pembeli ? $cartItem->pembeli->toArray() : null
            ]);
        });
        
        // Test cart add functionality
        Route::get('/debug-test-add/{barang_id}', function($barang_id) {
            $user = Auth::guard('web')->user();
            $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            // Check if barang exists
            $barang = \App\Models\Barang::find($barang_id);
            if (!$barang) {
                return response()->json(['error' => 'Barang not found']);
            }
            
            // Check if already in cart
            $existingItem = \App\Models\KeranjangBelanja::where('pembeli_id', $pembeliId)
                ->where('barang_id', $barang_id)
                ->first();
            
            if ($existingItem) {
                return response()->json([
                    'message' => 'Item already in cart',
                    'existing_item' => $existingItem->toArray()
                ]);
            }
            
            // Try to add to cart
            try {
                $cartItem = \App\Models\KeranjangBelanja::create([
                    'pembeli_id' => $pembeliId,
                    'barang_id' => $barang_id
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item added to cart',
                    'cart_item' => $cartItem->toArray(),
                    'user_id' => $user->id,
                    'pembeli_id' => $pembeliId,
                    'barang' => $barang->toArray()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Failed to add to cart',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
        
        // Clear all cart items for debugging
        Route::get('/debug-clear-cart', function() {
            $user = Auth::guard('web')->user();
            $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
            $pembeliId = $pembeli ? $pembeli->pembeli_id : $user->id;
            
            $deletedCount = \App\Models\KeranjangBelanja::where('pembeli_id', $pembeliId)->delete();
            
            return response()->json([
                'message' => 'Cart cleared',
                'deleted_count' => $deletedCount,
                'user_id' => $user->id,
                'pembeli_id' => $pembeliId
            ]);
        });
        
        // Check database tables
        Route::get('/debug-tables', function() {
            return response()->json([
                'keranjang_belanja_structure' => DB::select("DESCRIBE keranjang_belanja"),
                'barang_structure' => DB::select("DESCRIBE barang"),
                'pembeli_structure' => DB::select("DESCRIBE pembeli"),
                'users_structure' => DB::select("DESCRIBE users"),
                'keranjang_sample' => DB::table('keranjang_belanja')->limit(5)->get(),
                'barang_sample' => DB::table('barang')->limit(5)->get(),
                'pembeli_sample' => DB::table('pembeli')->limit(5)->get()
            ]);
        });
    });
}

// ========================================
// FALLBACK ROUTE
// ========================================

Route::fallback(function () {
    return view('errors.404');
});
