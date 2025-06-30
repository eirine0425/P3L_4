<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
use App\Http\Controllers\Api\PenitipBarangController;
use App\Http\Controllers\Api\PenitipTransaksiController;
use App\Http\Controllers\Api\ConsignorPickupController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\OwnerReportController;

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
Route::post('/mobile/login', [AuthController::class, 'mobileLogin']);
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
        'owner' => redirect()->route('owner.dashboard'),
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
    Route::get('/buyer/dashboard', function () {
        return view('buyer.dashboard');
    })->name('buyer.dashboard');
    
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
    
    // Cart selected items routes (consolidated)
    Route::prefix('buyer')->name('buyer.')->group(function () {
        Route::post('/cart/selected-items', [KeranjangBelanjaController::class, 'getSelectedItems'])->name('cart.selected-items');
        Route::post('/cart/prepare-checkout', [KeranjangBelanjaController::class, 'prepareCheckout'])->name('cart.prepare-checkout');
        
        // Alamat Selector Routes
        Route::get('/alamat/select', [WebViewController::class, 'alamatSelect'])->name('alamat.select');
        Route::get('/alamat/details/{id}', [WebViewController::class, 'alamatGetDetails'])->name('alamat.details');
        Route::get('/alamat/default', [WebViewController::class, 'getDefaultAlamat'])->name('alamat.default');
    });
    
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
    
    // ========================================
    // CHECKOUT ROUTES - ENHANCED
    // ========================================
    
    // Main checkout route
    Route::get('/checkout', function (Request $request) {
        $selectedItems = $request->input('selected_items', []);
        Log::debug('Selected items in checkout route', ['selected_items' => $selectedItems]);

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu item untuk checkout');
        }

        session(['checkout_selected_items' => $selectedItems]);

        return view('checkout.index', compact('selectedItems'));
    })->name('checkout.index');
    
    // Checkout show route with shipping calculation
    Route::get('/checkout/show', [WebViewController::class, 'showCheckout'])->name('checkout.show');
    
    // Process checkout
    Route::post('/checkout/process', [TransaksiController::class, 'store'])->name('checkout.process');
    
    // Thank you page
    Route::get('/checkout/thank-you/{transaction_id}', function($transactionId) {
        $transaction = \App\Models\Transaksi::with(['details.barang.kategoriBarang', 'alamat'])
            ->where('transaksi_id', $transactionId)
            ->where('pembeli_id', function($query) {
                $user = Auth::user();
                $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
                return $pembeli ? $pembeli->pembeli_id : $user->id;
            })
            ->firstOrFail();
            
        return view('checkout.thank-you', compact('transaction'));
    })->name('checkout.thank-you');
    
    // API Routes for checkout
    Route::prefix('api/checkout')->name('api.checkout.')->group(function () {
        Route::post('/calculate-shipping', [WebViewController::class, 'calculateShipping'])->name('calculate-shipping');
        Route::post('/validate-address', [WebViewController::class, 'validateAddress'])->name('validate-address');
        Route::get('/payment-methods', [WebViewController::class, 'getPaymentMethods'])->name('payment-methods');
    });
    
    // Example/Demo Routes
    Route::get('/dashboard/alamat-selector-demo', function () {
        return view('examples.alamat-selector-usage');
    })->name('buyer.alamat.selector.demo');
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
    Route::post('/items/{id}/extend', [DashboardConsignorController::class, 'extendItem'])->name('items.extend');
    
    // Fallback routes for missing views
    Route::get('/dashboard/transaksi/fallback', function () {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.index']);
    })->name('consignor.transactions.fallback');
    
    Route::get('/dashboard/transaksi/fallback/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.show', 'id' => $id]);
    })->name('consignor.transactions.show.fallback');
    
    // Consignor Pickup Routes
    Route::get('/dashboard/consignor/pickup', [App\Http\Controllers\Api\ConsignorPickupController::class, 'index'])->name('consignor.pickup');
    Route::post('/dashboard/consignor/schedule-pickup', [App\Http\Controllers\Api\ConsignorPickupController::class, 'schedulePickup'])->name('consignor.schedule-pickup');
    Route::get('/dashboard/consignor/pickup/{id}', [App\Http\Controllers\Api\ConsignorPickupController::class, 'showPickupDetail'])->name('consignor.pickup.detail');
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
        Route::get('/verification', [DashboardWarehouseController::class, 'verification'])->name('verification');
        // Route untuk konfirmasi pengambilan barang oleh pegawai gudang
    Route::post('/dashboard/warehouse/confirm-pickup/{transaksi}', [App\Http\Controllers\Api\DashboardWarehouseController::class, 'confirmPickup'])->name('warehouse.confirm.pickup');
    Route::post('/dashboard/warehouse/confirm-delivery/{transaksi}', [App\Http\Controllers\Api\DashboardWarehouseController::class, 'confirmDelivery'])->name('warehouse.confirm.delivery');
        // KELOLA PESANAN ROUTE - MISSING ROUTE ADDED
        Route::get('/kelola-pesanan', [DashboardWarehouseController::class, 'kelolaPesanan'])->name('kelola-pesanan');
        Route::post('/kelola-pesanan/schedule', [DashboardWarehouseController::class, 'scheduleDelivery'])->name('kelola-pesanan.schedule');
 Route::get('/dashboard/warehouse/daftar-transaksi', [App\Http\Controllers\Api\DashboardWarehouseController::class, 'daftarTransaksi'])->name('warehouse.daftar.transaksi');
    Route::post('/dashboard/warehouse/mark-ready/{transaksi}', [App\Http\Controllers\Api\DashboardWarehouseController::class, 'markReadyForPickup'])->name('warehouse.mark.ready');
        // PICKUP SCHEDULING ROUTES - ADD THESE MISSING ROUTES
        Route::get('/pickup-scheduling', [DashboardWarehouseController::class, 'pickupScheduling'])->name('pickup-scheduling');
        Route::post('/pickup-scheduling', [DashboardWarehouseController::class, 'storePickupSchedule'])->name('pickup-scheduling.store');
        Route::get('/pickup-scheduling/create', [DashboardWarehouseController::class, 'createPickupSchedule'])->name('pickup-scheduling.create');
        Route::get('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'showPickupSchedule'])->name('pickup-scheduling.show');
        Route::put('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'updatePickupSchedule'])->name('pickup-scheduling.update');
        Route::delete('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'cancelPickupSchedule'])->name('pickup-scheduling.cancel');
        Route::post('/dashboard/warehouse/confirm-received/{orderId}', [DashboardWarehouseController::class, 'confirmItemReceived'])->name('dashboard.warehouse.confirm-received');
        // SEARCH FUNCTIONALITY ROUTES
        Route::get('/export', [DashboardWarehouseController::class, 'exportResults'])->name('export');
        Route::post('/bulk-update', [DashboardWarehouseController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/save-search', [DashboardWarehouseController::class, 'saveSearch'])->name('save-search');
        Route::get('/saved-searches', [DashboardWarehouseController::class, 'getSavedSearches'])->name('saved-searches');

         Route::get('/transaksi-siap-ambil', [App\Http\Controllers\Api\DashboardWarehouseController::class, 'transaksiSiapAmbil'])->name('warehouse.transaksi.siap-ambil');
        
Route::get('/shipments', [DashboardWarehouseController::class, 'shipments'])->name('shipments');
    Route::get('/shipments/{id}', [DashboardWarehouseController::class, 'showShipment'])->name('shipments.show');
    Route::get('/shipments/{id}/create', [DashboardWarehouseController::class, 'createShipment'])->name('shipments.create');
    Route::post('/shipments', [DashboardWarehouseController::class, 'storeShipment'])->name('shipments.store');
    Route::put('/shipments/{id}/status', [DashboardWarehouseController::class, 'updateShipmentStatus'])->name('shipments.update-status');
    Route::put('/shipments/{id}/courier', [DashboardWarehouseController::class, 'assignCourier'])->name('shipments.assign-courier');

    // SHIPPING VALIDATION & BULK OPERATIONS - TAMBAHAN BARU
    Route::post('/validate-shipping-time', [DashboardWarehouseController::class, 'validateShippingTime'])->name('validate-shipping-time');
    Route::post('/bulk-schedule-shipments', [DashboardWarehouseController::class, 'bulkScheduleShipments'])->name('bulk-schedule-shipments');
 Route::get('/pickup-scheduling', [DashboardWarehouseController::class, 'pickupScheduling'])->name('pickup.scheduling');
    Route::post('/pickup-scheduling', [DashboardWarehouseController::class, 'storePickupSchedule'])->name('pickup.schedule.store');
    Route::get('/pickup-scheduling/create', [DashboardWarehouseController::class, 'createPickupSchedule'])->name('pickup.schedule.create');
    Route::get('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'showPickupSchedule'])->name('pickup.schedule.show');
    Route::put('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'updatePickupSchedule'])->name('pickup.schedule.update');
    Route::delete('/pickup-scheduling/{id}', [DashboardWarehouseController::class, 'cancelPickupSchedule'])->name('pickup.schedule.cancel');
    
    // API endpoints for AJAX
    Route::get('/api/penitip/{id}/items', [DashboardWarehouseController::class, 'getPenitipItems'])->name('api.penitip.items');
    Route::post('/api/pickup-schedule/validate', [DashboardWarehouseController::class, 'validatePickupSchedule'])->name('api.pickup.validate');
        // PDF PRINTING ROUTES
        Route::get('/print-note/{id}', [DashboardWarehouseController::class, 'printConsignmentNote'])->name('print-note');
        Route::post('/print-bulk-notes', [DashboardWarehouseController::class, 'printBulkConsignmentNotes'])->name('print-bulk-notes');
        
        // CONSIGNMENT MANAGEMENT
        Route::get('/consignment/create', [DashboardWarehouseController::class, 'createConsignmentItem'])->name('consignment.create');
        Route::post('/consignment', [DashboardWarehouseController::class, 'storeConsignmentItem'])->name('consignment.store');
        Route::get('/consignment/transactions', [DashboardWarehouseController::class, 'consignmentTransactions'])->name('consignment.transactions');
        Route::get('/consignment/transaction/{id}', [DashboardWarehouseController::class, 'showConsignmentTransaction'])->name('consignment.transaction.show');
        
        // ITEM MANAGEMENT
        Route::get('/items/{id}', [DashboardWarehouseController::class, 'showItem'])->name('item.show');
        Route::get('/items/{id}/edit', [DashboardWarehouseController::class, 'editItem'])->name('item.edit');
        Route::put('/items/{id}', [DashboardWarehouseController::class, 'updateItem'])->name('item.update');
        Route::put('/items/{id}/update-status', [DashboardWarehouseController::class, 'updateItemStatus'])->name('item.update-status');
        Route::put('/items/{id}/extend', [DashboardWarehouseController::class, 'extendConsignment'])->name('item.extend');
        
        // Alternative item routes for compatibility
        Route::get('/item/{id}', [DashboardWarehouseController::class, 'showItem'])->name('item.show.alt');
        Route::put('/item/{id}/status', [DashboardWarehouseController::class, 'updateItemStatus'])->name('item.update-status.alt');
        
        // SHIPMENTS MANAGEMENT
        Route::get('/shipments/{id}', [DashboardWarehouseController::class, 'showShipment'])->name('shipments.show');
        Route::get('/shipments/{id}/create', [DashboardWarehouseController::class, 'createShipment'])->name('shipments.create');
        Route::post('/shipments', [DashboardWarehouseController::class, 'storeShipment'])->name('shipments.store');
        Route::put('/shipments/{id}/status', [DashboardWarehouseController::class, 'updateShipmentStatus'])->name('shipments.update-status');
        Route::put('/shipments/{id}/courier', [DashboardWarehouseController::class, 'assignCourier'])->name('shipments.assign-courier');
        // Tambahkan di dalam grup 'dashboard/warehouse'
Route::get('/shipments-ready', [DashboardWarehouseController::class, 'shipmentsReady'])->name('shipments-ready');

        // TRANSACTION MANAGEMENT
        Route::post('/transaction/{id}/shipping', [DashboardWarehouseController::class, 'createShippingSchedule'])->name('create-shipping');
        Route::post('/transaction/{id}/pickup', [DashboardWarehouseController::class, 'createPickupSchedule'])->name('create-pickup');
        Route::get('/transaction/{id}/sales-note', [DashboardWarehouseController::class, 'generateSalesNote'])->name('sales-note');
        Route::post('/transaction/{id}/confirm', [DashboardWarehouseController::class, 'confirmItemReceived'])->name('confirm-received');
        Route::post('/transaction/{id}/status', [DashboardWarehouseController::class, 'updateTransactionStatus'])->name('update-transaction-status');

        // PICKUP MANAGEMENT
        Route::get('/pickup', [DashboardWarehouseController::class, 'itemPickup'])->name('item-pickup');
        Route::get('/pickup/{id}/detail', [DashboardWarehouseController::class, 'showPickupDetail'])->name('pickup.detail');
        Route::post('/pickup/{id}/confirm', [DashboardWarehouseController::class, 'confirmItemPickup'])->name('pickup.confirm');
        Route::post('/pickup/bulk-confirm', [DashboardWarehouseController::class, 'bulkConfirmPickup'])->name('pickup.bulk-confirm');
        Route::get('/pickup/report', [DashboardWarehouseController::class, 'generatePickupReport'])->name('pickup.report');
        Route::get('/pickup-history', [DashboardWarehouseController::class, 'pickupHistory'])->name('pickup-history');
        Route::get('/pickup-receipt/{id}', [DashboardWarehouseController::class, 'generatePickupReceipt'])->name('pickup-receipt');
        
        // PICKUP FORM ROUTES
        Route::get('/item/{id}/record-pickup', [DashboardWarehouseController::class, 'showPickupForm'])->name('show-pickup-form');
        Route::post('/item/{id}/record-pickup', [DashboardWarehouseController::class, 'recordItemPickup'])->name('record-pickup');
        
        // COURIER NOTE ROUTE - Add this new route
        Route::get('/courier-note/{id}', [DashboardWarehouseController::class, 'generateCourierNote'])->name('courier-note');
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
    })->name('cs.discussions');
    
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
    
    // Alternative admin route for compatibility
    Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
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

// ========================================
// OWNER ROUTES - FIXED
// ========================================

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.owner.index');
    })->name('owner.dashboard');
    
    // Reports
    Route::get('/reports/expired-items', function () {
        return view('dashboard.owner.reports.expired-items');
    })->name('owner.reports.expired-items');
    
    // PDF download route - FIXED
    Route::get('/reports/expired-items/pdf', [ReportController::class, 'expiredItemsReport'])
        ->name('owner.reports.expired-items.pdf');
    
    // Sales Report by Category with Hunter
    Route::get('/sales-report-category-hunter/form', [OwnerReportController::class, 'salesReportByCategoryWithHunterForm'])
        ->name('dashboard.owner.sales-report-category-hunter-form');
    
    Route::post('/sales-report-category-hunter', [OwnerReportController::class, 'salesReportByCategoryWithHunter'])
        ->name('dashboard.owner.sales-report-category-hunter');
    
    Route::get('/sales-report-category-hunter/pdf', [OwnerReportController::class, 'salesReportByCategoryWithHunterPDF'])
        ->name('dashboard.owner.sales-report-category-hunter-pdf');
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
// API ROUTES FOR AJAX CALLS
// ========================================

Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    // Address API routes
    Route::get('/alamat/checkout', [AlamatController::class, 'getForCheckout'])->name('alamat.checkout');
    Route::get('/alamat/default', [AlamatController::class, 'getDefault'])->name('alamat.default');
    
    // Cart API routes
    Route::post('/cart/selected-items', [KeranjangBelanjaController::class, 'getSelectedItems'])->name('cart.selected-items');
    Route::get('/cart/count', [KeranjangBelanjaController::class, 'getCartCount'])->name('cart.count');
    
    // Shipping calculation
    Route::post('/shipping/calculate', [WebViewController::class, 'calculateShipping'])->name('shipping.calculate');
    
    // Report API routes - ADDED
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/reports/expired-items/data', [ReportController::class, 'expiredItemsData'])->name('reports.expired-items.data');
        Route::get('/reports/expired-items/filters', [ReportController::class, 'getFilterOptions'])->name('reports.expired-items.filters');
    });
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
        
        // Test database connection
        Route::get('/debug-database', function() {
            try {
                $connection = DB::connection()->getPdo();
                $tables = DB::select('SHOW TABLES');
                
                return response()->json([
                    'status' => 'connected',
                    'database' => config('database.connections.mysql.database'),
                    'tables_count' => count($tables),
                    'tables' => array_map(function($table) {
                        return array_values((array)$table)[0];
                    }, $tables)
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        });
        
        // Test expired items query
        Route::get('/debug-expired-items', function() {
            try {
                $query = \App\Models\Barang::with(['penitip.user', 'kategoriBarang'])
                    ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                    ->where('status', '!=', 'diambil_kembali')
                    ->where('status', '!=', 'terjual');
                
                $items = $query->get();
                
                return response()->json([
                    'status' => 'success',
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                    'count' => $items->count(),
                    'items' => $items->take(5)->toArray()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    });
}

// ========================================
// FALLBACK ROUTE - FIXED
// ========================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
