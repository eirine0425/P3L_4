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

// PERBAIKAN: Mengganti route CS dashboard dengan data yang benar
Route::middleware(['auth', 'role:cs'])->get('/dashboard/cs', function () {
    try {
        $totalPenitip = Penitip::count();
        $totalPembeli = Pembeli::count();
        $verifikasiTertunda = Barang::where('status', 'pending')->count();
        $diskusiBelumDibalas = DiskusiProduk::whereNull('balasan')->count();
        
        // Get latest discussions with proper relationships
        $diskusiTerbaru = DiskusiProduk::with(['user', 'barang'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get items pending verification
        $barangUntukVerifikasi = Barang::where('status', 'pending')
            ->with(['kategori', 'penitip.user'])
            ->take(5)
            ->get();

        return view('dashboard.cs.index', compact(
            'totalPenitip',
            'totalPembeli',
            'verifikasiTertunda',
            'diskusiBelumDibalas',
            'diskusiTerbaru',
            'barangUntukVerifikasi'
        ));
    } catch (\Exception $e) {
        // Fallback with default values if there are any database issues
        return view('dashboard.cs.index', [
            'totalPenitip' => 0,
            'totalPembeli' => 0,
            'verifikasiTertunda' => 0,
            'diskusiBelumDibalas' => 0,
            'diskusiTerbaru' => collect(),
            'barangUntukVerifikasi' => collect()
        ]);
    }
})->name('dashboard.cs');

// Protected Routes
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();
    $role = strtolower($user->role->nama_role ?? '');

    return match ($role) {
        'owner' => redirect()->route('dashboard.owner'),
        'admin' => redirect()->route('dashboard.admin'),
        'pegawai', 'gudang' => redirect()->route('dashboard.warehouse'),
        'cs' => redirect()->route('dashboard.cs'),
        'penitip', 'penjual' => redirect()->route('dashboard.consignor'),
        'organisasi' => redirect()->route('dashboard.organization'),
        'pembeli' => redirect()->route('dashboard.buyer'),
        default => abort(403, 'Role tidak dikenali')
    };
})->name('dashboard');

// PERBAIKAN: Menggunakan controller untuk dashboard buyer
Route::middleware(['auth', 'role:pembeli'])->get('/dashboard/buyer', [DashboardBuyerController::class, 'index'])->name('dashboard.buyer');

// Add other role-specific dashboard routes
Route::get('/dashboard/owner', function () {
    return view('dashboard.owner.index');
})->name('dashboard.owner');

Route::get('/dashboard/admin', function () {
    return view('dashboard.admin.index');
})->name('dashboard.admin');

Route::get('/dashboard/warehouse', function () {
    return view('dashboard.warehouse.index');
})->name('dashboard.warehouse');

Route::get('/dashboard/consignor', function () {
    return view('dashboard.consignor.index');
})->name('dashboard.consignor');

Route::get('/dashboard/organization', function () {
    return view('dashboard.organization.index');
})->name('dashboard.organization');

// Profile Routes
Route::get('/dashboard/profil', function () {
    return view('errors.missing-view', ['view' => 'dashboard.profile.show']);
})->name('profile.show');

Route::put('/dashboard/profil', [UserController::class, 'update'])->name('profile.update');

// Buyer Routes
Route::middleware(['auth', 'role:pembeli'])->group(function () {
    // Transaction Routes - PERBAIKAN: Menggunakan controller
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
Route::middleware(['role:Penitip'])->group(function () {
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
Route::middleware(['role:CS'])->group(function () {
    // Tambahkan route untuk diskusi
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
Route::middleware(['role:Pegawai Gudang'])->group(function () {
    // Consignment Items Routes
    Route::get('/dashboard/barang-titipan', function () {
        return view('errors.missing-view', ['view' => 'dashboard.warehouse.items.index']);
    })->name('warehouse.items');
    
    Route::get('/dashboard/barang-titipan/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.warehouse.items.show', 'id' => $id]);
    })->name('warehouse.items.show');
    
    // Shipping Routes
    Route::get('/dashboard/pengiriman', function () {
        return view('errors.missing-view', ['view' => 'dashboard.warehouse.shipments.index']);
    })->name('warehouse.shipments');
    
    Route::get('/dashboard/pengiriman/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.warehouse.shipments.show', 'id' => $id]);
    })->name('warehouse.shipments.show');
});

// Admin Routes
Route::middleware(['role:Admin'])->group(function () {
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
});

// Owner Routes
Route::middleware(['role:Owner'])->group(function () {
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
