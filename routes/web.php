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
use App\Http\Controllers\Dashboard\Owner\RequestDonasiController;
use App\Http\Controllers\Api\DashboardOrganisasiController;
use App\Http\Controllers\Api\DashboardAdminController;
use App\Http\Controllers\Api\DashboardConsignorController;
use App\Http\Controllers\Api\DashboardOwnerController; // ADDED: Import Owner Controller
use App\Http\Controllers\Api\BuyerProfileController;
use App\Http\Controllers\Api\DashboardProfileController;
use App\Http\Controllers\Api\PenitipBarangController;
use App\Http\Controllers\Api\PenitipTransaksiController;
use App\Http\Controllers\Api\ConsignorPickupController;
use App\Http\Controllers\Api\UnsoldItemPickupController;

use App\Http\Controllers\Api\RatingController;

use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;

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
    // Untuk web route
    Route::get('/buyer/alamat/select', [\App\Http\Controllers\Api\AlamatController::class, 'getForSelection'])->name('buyer.alamat.select');

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
    
    // Payment routes
    Route::get('/payment/{transaksi_id}', [TransaksiController::class, 'show'])->name('payment.show');
    Route::post('/payment/{transaksi_id}/upload', [TransaksiController::class, 'uploadProof'])->name('payment.upload');
    Route::get('/payment/{transaksi_id}/cancel', [TransaksiController::class, 'cancelTransaction'])->name('transaction.cancel');

    // Thank you page - REDIRECT LANGSUNG KE PAYMENT
    Route::get('/checkout/thank-you/{transaction_id}', function($transactionId) {
        // Langsung redirect ke payment countdown tanpa validasi tambahan
        return redirect()->route('checkout.payment', ['id' => $transactionId])
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
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
    
    // Payment countdown page - PERBAIKI INI
    Route::get('/checkout/payment/{id}', [WebViewController::class, 'showPaymentCountdown'])
        ->name('checkout.payment');
    
    // Check transaction status endpoint
    Route::get('/checkout/check-status/{transaction_id}', [WebViewController::class, 'checkTransactionStatus'])
        ->name('checkout.check-status');
    
    // Cancel transaction endpoint
    Route::get('/checkout/cancel/{transaction_id}', [WebViewController::class, 'cancelTransaction'])
        ->name('checkout.cancel');
    
    // Cancelled transaction page
    Route::get('/checkout/cancelled/{transaction_id}', [WebViewController::class, 'showCancelledPage'])
        ->name('checkout.cancelled');
    
    // Upload payment proof
    Route::post('/buyer/transactions/{id}/upload-payment', [WebViewController::class, 'uploadBuktiPembayaran'])
        ->name('buyer.transactions.upload-payment');

    // Tambahkan route untuk success page
    Route::get('/checkout/success/{transaction_id}', [WebViewController::class, 'showSuccessPage'])
        ->name('checkout.success');

    // Buyer Rating Routes
    Route::get('/dashboard/buyer/profile/ratings', [BuyerProfileController::class, 'showRatings'])->name('buyer.profile.ratings');
    Route::post('/dashboard/buyer/rating/submit', [BuyerProfileController::class, 'submitRating'])->name('buyer.rating.submit');
    Route::put('/dashboard/buyer/rating/{rating}/update', [BuyerProfileController::class, 'updateRating'])->name('buyer.rating.update');
    Route::delete('/dashboard/buyer/rating/{rating}/delete', [BuyerProfileController::class, 'deleteRating'])->name('buyer.rating.delete');
    
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
    
    // Extension Route - NEW
    Route::post('/dashboard/barang-saya/{id}/extend', [DashboardConsignorController::class, 'extendItem'])->name('items.extend');

    // Consignment Transaction Routes - Enhanced
    Route::get('/dashboard/transaksi', [DashboardConsignorController::class, 'transactions'])->name('consignor.transactions');
    Route::get('/dashboard/transaksi/{id}', [DashboardConsignorController::class, 'showTransaction'])->name('consignor.transactions.show');
    
    // Extension Route - NEW
    Route::post('/dashboard/transaksi/extend', [DashboardConsignorController::class, 'extendTransaction'])->name('consignor.transactions.extend');
    
    // Expiring Transactions Routes - NEW
    Route::get('/dashboard/transaksi-berakhir', [DashboardConsignorController::class, 'expiringTransactions'])->name('consignor.transactions.expiring');
    Route::get('/dashboard/transaksi-berakhir/{id}', [DashboardConsignorController::class, 'showExpiringTransaction'])->name('consignor.transactions.expiring.show');
    Route::post('/dashboard/transaksi-berakhir/{id}/extend', [DashboardConsignorController::class, 'extendTransaction'])->name('consignor.transactions.extend');
    
    // Fallback routes for missing views
    Route::get('/dashboard/transaksi/fallback', function () {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.index']);
    })->name('consignor.transactions.fallback');
    
    Route::get('/dashboard/transaksi/fallback/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.consignor.transactions.show', 'id' => $id]);
    })->name('consignor.transactions.show.fallback');
    
    // Ratings Routes for Consignors
    Route::get('/dashboard/rating-diterima', [DashboardConsignorController::class, 'showRatings'])->name('consignor.ratings');
    Route::get('/dashboard/rating-diterima/{id}', [DashboardConsignorController::class, 'showRatingDetail'])->name('consignor.ratings.show');
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
        
        // NEW ROUTES FOR VERIFIED TRANSACTIONS
        Route::get('/verified-transactions', [DashboardWarehouseController::class, 'verifiedTransactions'])->name('verified-transactions');
        Route::post('/transaction/{id}/prepare', [DashboardWarehouseController::class, 'updateTransactionToPrepared'])->name('transaction.prepare');
        Route::post('/transactions/bulk-prepare', [DashboardWarehouseController::class, 'bulkUpdateToPrepared'])->name('transactions.bulk-prepare');
        
        // NEW ROUTES FOR SEARCH FUNCTIONALITY
        Route::get('/export', [DashboardWarehouseController::class, 'exportResults'])->name('export');
        Route::post('/bulk-update', [DashboardWarehouseController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/save-search', [DashboardWarehouseController::class, 'saveSearch'])->name('save-search');
        Route::get('/saved-searches', [DashboardWarehouseController::class, 'getSavedSearches'])->name('saved-searches');
        
        // PDF PRINTING ROUTES - FIXED
        Route::get('/print/item/{id}', [DashboardWarehouseController::class, 'printItemDetail'])->name('print-item-detail');
        Route::post('/print/selected-items', [DashboardWarehouseController::class, 'printSelectedItems'])->name('print-selected-items');
        Route::get('/print/consigned-items', [DashboardWarehouseController::class, 'printConsignedItems'])->name('print-consigned-items');
        Route::get('/print/inventory-summary', [DashboardWarehouseController::class, 'printInventorySummary'])->name('print-inventory-summary');
        
        // Legacy print routes for backward compatibility
        Route::get('/print-note/{id}', [DashboardWarehouseController::class, 'printItemDetail'])->name('print-note');
        Route::post('/print-bulk-notes', [DashboardWarehouseController::class, 'printSelectedItems'])->name('print-bulk-notes');
        
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
        
        // Shipments Management (Features 1 & 2)
        Route::get('/shipments', [DashboardWarehouseController::class, 'shipments'])->name('shipments'); // Feature 1
        Route::get('/shipments/{id}', [DashboardWarehouseController::class, 'showShipment'])->name('shipments.show');
        Route::get('/shipments/{id}/create', [DashboardWarehouseController::class, 'createShipment'])->name('shipments.create'); // Feature 2
        Route::post('/shipments', [DashboardWarehouseController::class, 'storeShipment'])->name('shipments.store'); // Feature 2
        Route::put('/shipments/{id}/status', [DashboardWarehouseController::class, 'updateShipmentStatus'])->name('shipments.update-status');
        Route::put('/shipments/{id}/courier', [DashboardWarehouseController::class, 'assignCourier'])->name('shipments.assign-courier'); // Feature 2

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

        Route::get('/pickup', [DashboardWarehouseController::class, 'itemPickup'])->name('item-pickup');
        Route::get('/pickup/{id}/detail', [DashboardWarehouseController::class, 'showPickupDetail'])->name('pickup.detail');
        Route::post('/pickup/{id}/confirm', [DashboardWarehouseController::class, 'confirmItemPickup'])->name('pickup.confirm');
        Route::post('/pickup/bulk-confirm', [DashboardWarehouseController::class, 'bulkConfirmPickup'])->name('pickup.bulk-confirm');
        Route::get('/pickup/report', [DashboardWarehouseController::class, 'generatePickupReport'])->name('pickup.report');

        // Add this route for extending consignment
        Route::put('/item/{id}/extend', [DashboardWarehouseController::class, 'extendConsignment'])
            ->name('item.extend');
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
    
    // Merchandise Claims Routes - NEW
    Route::prefix('dashboard/cs/merchandise-claims')->name('cs.merchandise.claims.')->group(function () {
        Route::get('/', [DashboardCSController::class, 'merchandiseClaims'])->name('index');
        Route::get('/{pembeliId}/{merchId}/{tanggalPenukaran}', [DashboardCSController::class, 'showMerchandiseClaim'])->name('show');
        Route::put('/{pembeliId}/{merchId}/{tanggalPenukaran}', [DashboardCSController::class, 'updateMerchandiseClaim'])->name('update');
        Route::post('/bulk-update', [DashboardCSController::class, 'bulkUpdateMerchandiseClaims'])->name('bulk-update');
    });
    
    // Alternative merchandise claims routes
    Route::get('/dashboard/cs/merchandise-claims', [DashboardCSController::class, 'merchandiseClaims'])->name('cs.merchandise.claims');
    Route::get('/dashboard/cs/merchandise-claims/{pembeliId}/{merchId}/{tanggalPenukaran}', [DashboardCSController::class, 'showMerchandiseClaim'])->name('cs.merchandise.claims.show');
    Route::put('/dashboard/cs/merchandise-claims/{pembeliId}/{merchId}/{tanggalPenukaran}', [DashboardCSController::class, 'updateMerchandiseClaim'])->name('cs.merchandise.claims.update');
    
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

// Owner Dashboard Routes
Route::middleware(['auth', 'role:owner'])->prefix('dashboard/owner')->name('dashboard.owner.')->group(function () {

    // Donasi Routes
    Route::get('/donasi', function () {
        return view('dashboard.owner.donasi.index');
    })->name('donasi.index');

    Route::get('/donasi/print-report', [App\Http\Controllers\Api\DashboardOwnerController::class, 'printDonasiReport'])->name('donasi.print');
    Route::get('/donasi/report-data', [DashboardOwnerController::class, 'donasiReport'])->name('donasi.report-data');
    Route::get('/donasi/export-data', [DashboardOwnerController::class, 'exportDonasiReport'])->name('donasi.export-data');

    // Request Donasi Routes
    Route::get('/request-donasi', function () {
        return view('dashboard.owner.request-donasi.index');
    })->name('request-donasi.index');

    Route::get('/request-donasi/print-report', [DashboardOwnerController::class, 'printRequestDonasiReport'])->name('request-donasi.print');
    Route::get('/request-donasi/report-data', [DashboardOwnerController::class, 'requestDonasiReport'])->name('request-donasi.report-data');
    Route::get('/request-donasi/export-data', [DashboardOwnerController::class, 'exportRequestDonasiReport'])->name('request-donasi.export-data');

    // Transaksi Penitipan Routes
    Route::get('/transaksi-penitipan', [DashboardOwnerController::class, 'transaksiPenitipanIndex'])->name('transaksi-penitipan.index');
    Route::get('/transaksi-penitipan/report', [DashboardOwnerController::class, 'transaksiPenitipanReport'])->name('transaksi-penitipan.report');
    Route::get('/transaksi-penitipan/print', [DashboardOwnerController::class, 'printTransaksiPenitipanReport'])->name('transaksi-penitipan.print');
    Route::get('/transaksi-penitipan/export', [DashboardOwnerController::class, 'exportTransaksiPenitipanReport'])->name('transaksi-penitipan.export');

});

// Alternative admin route for compatibility
Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');

// ========================================
// OWNER ROUTES - CORRECTED TO USE CONTROLLER
// ========================================

Route::middleware(['auth', 'role:owner'])->group(function () {
    // Main Dashboard Route - Returns view, not JSON
    Route::get('/dashboard/owner', function () {
        return view('dashboard.owner.index');
    })->name('dashboard.owner');

    // Additional Owner Management Routes
    Route::prefix('dashboard/owner')->name('owner.')->group(function () {
        Route::get('/transactions', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.transactions.index']);
        })->name('transactions');
        Route::get('/consignors', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.consignors.index']);
        })->name('consignors');
        Route::get('/analytics', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.analytics.index']);
        })->name('analytics');
        Route::get('/settings', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.settings.index']);
        })->name('settings');
        Route::get('/financial-summary', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.financial.index']);
        })->name('financial-summary');
    });
    
    // Dashboard Data API Routes - These return JSON
    Route::prefix('api/dashboard/owner')->name('api.dashboard.owner.')->group(function () {
        Route::get('/data', [DashboardOwnerController::class, 'index'])->name('data');
        Route::get('/sales-report', [DashboardOwnerController::class, 'salesReport'])->name('sales-report');
        Route::get('/profit-report', [DashboardOwnerController::class, 'profitReport'])->name('profit-report');
    });

    // Print Report Route - NEW
    Route::get('/dashboard/owner/print-report', [DashboardOwnerController::class, 'printReport'])->name('owner.print-report');
    
    // Legacy Donation Routes (keeping for backward compatibility)
    Route::get('/dashboard/donasi', function () {
        return view('errors.missing-view', ['view' => 'dashboard.owner.donations.index']);
    })->name('owner.donations');

    Route::get('/dashboard/donasi/{id}', function ($id) {
        return view('errors.missing-view', ['view' => 'dashboard.owner.donations.show', 'id' => $id]);
    })->name('owner.donations.show');

    // Enhanced Report Routes
    Route::prefix('dashboard/laporan')->name('owner.reports.')->group(function () {
        Route::get('/penjualan', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.reports.sales']);
        })->name('sales');
        Route::get('/komisi', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.reports.commission']);
        })->name('commission');
        Route::get('/stok', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.reports.stock']);
        })->name('stock');
        Route::get('/kategori', function () {
            return view('errors.missing-view', ['view' => 'dashboard.owner.reports.category']);
        })->name('category');
    });
    
    // Rating Reports

    // Donation Report Routes - TAMBAHKAN INI
    Route::get('/donasi-report', [DashboardOwnerController::class, 'donasiReport'])->name('donasi.report');
    Route::get('/donasi-export', [DashboardOwnerController::class, 'exportDonasiReport'])->name('donasi.export');
});

// ========================================
// API ROUTES FOR DASHBOARD DATA
// ========================================

Route::middleware(['auth'])->prefix('api/dashboard')->name('api.dashboard.')->group(function () {
    // Owner API Routes
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/owner', [DashboardOwnerController::class, 'index'])->name('owner');
        Route::get('/owner/export', [DashboardOwnerController::class, 'exportReport'])->name('owner.export');
        Route::get('/owner/sales-report', [DashboardOwnerController::class, 'salesReport'])->name('owner.sales-report');
        Route::get('/owner/profit-report', [DashboardOwnerController::class, 'profitReport'])->name('owner.profit-report');
        Route::get('/owner/donasi-report', [DashboardOwnerController::class, 'donasiReport'])->name('owner.donasi-report');
        Route::get('/owner/donasi-export', [DashboardOwnerController::class, 'exportDonasiReport'])->name('owner.donasi-export');
    });
    
    // Other dashboard API routes
    Route::get('/admin', [DashboardAdminController::class, 'index'])->middleware('role:admin');
    Route::get('/warehouse', [DashboardWarehouseController::class, 'index'])->middleware('role:pegawai gudang');
    Route::get('/cs', [DashboardCSController::class, 'index'])->middleware('role:cs');
    Route::get('/consignor', [DashboardConsignorController::class, 'index'])->middleware('role:penitip');
    Route::get('/buyer', [DashboardBuyerController::class, 'index'])->middleware('role:pembeli');
    Route::get('/organization', [DashboardOrganisasiController::class, 'index'])->middleware('role:organisasi');
    Route::get('/hunter', [DashboardHunterController::class, 'index'])->middleware('role:hunter');
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
    
    // TAMBAHKAN ROUTE API UNTUK COUNTDOWN
    Route::get('/transactions/{id}/status', [WebViewController::class, 'checkTransactionStatus'])->name('transactions.status');
    Route::post('/transactions/{id}/auto-cancel', [WebViewController::class, 'autoCancelExpiredTransaction'])->name('transactions.auto-cancel');
});

// ========================================
// DEBUG ROUTES (DEVELOPMENT ONLY)
// ========================================

if (config('app.debug')) {
    // Add these debugging routes (remove in production)
    Route::get('/debug/transactions', [DashboardWarehouseController::class, 'debugTransactions'])->name('debug.transactions');
    Route::get('/debug/transaction/{id}', [DashboardWarehouseController::class, 'getTransactionDetails'])->name('debug.transaction.details');

    // Updated PDF Print Routes with better error handling
    Route::get('/warehouse/consignment-note/{id}/print', [DashboardWarehouseController::class, 'printConsignmentNote'])->name('warehouse.consignment-note.print');
    Route::get('/warehouse/consignment-note/{id}/preview', [DashboardWarehouseController::class, 'previewConsignmentNote'])->name('warehouse.consignment-note.preview');
    Route::post('/warehouse/consignment-notes/bulk-print', [DashboardWarehouseController::class, 'bulkPrintConsignmentNotes'])->name('warehouse.consignment-notes.bulk-print');
    Route::get('/warehouse/shipping-label/{id}/print', [DashboardWarehouseController::class, 'printShippingLabel'])->name('warehouse.shipping-label.print');
    Route::get('/warehouse/inventory-report/print', [DashboardWarehouseController::class, 'printInventoryReport'])->name('warehouse.inventory-report.print');

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

        Route::get('/dashboard/verified-transactions', [KeranjangBelanjaController::class, 'verifiedTransactions'])->name('dashboard.verified-transactions');
        
        // Debug ratings
        Route::get('/debug-ratings', function() {
            return response()->json([
                'ratings_structure' => DB::select("DESCRIBE ratings"),
                'ratings_sample' => DB::table('ratings')->limit(5)->get(),
                'barang_with_ratings' => \App\Models\Barang::withCount('ratings')->orderBy('ratings_count', 'desc')->limit(5)->get(),
                'penitip_with_ratings' => \App\Models\Penitip::withCount(['barang', 'barang.ratings'])->orderBy('barang_count', 'desc')->limit(5)->get()
            ]);
        });

        // Test checkout flow
        Route::get('/debug-checkout', function() {
            $user = Auth::guard('web')->user();
            $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
            
            if (!$pembeli) {
                return response()->json(['error' => 'Pembeli not found']);
            }
            
            $cartItems = \App\Models\KeranjangBelanja::with(['barang.kategoriBarang'])
                ->where('pembeli_id', $pembeli->pembeli_id)
                ->get();
            
            $alamat = \App\Models\Alamat::where('pembeli_id', $pembeli->pembeli_id)
                ->where('is_default', true)
                ->first();
            
            return response()->json([
                'user' => $user->toArray(),
                'pembeli' => $pembeli->toArray(),
                'cart_items' => $cartItems->toArray(),
                'default_alamat' => $alamat ? $alamat->toArray() : null,
                'cart_count' => $cartItems->count(),
                'subtotal' => $cartItems->sum(function($item) {
                    return $item->barang->harga;
                })
            ]);
        });
    });
}

// Add the route for the donation hunter report
Route::get('/dashboard/owner/donasi-hunter', [App\Http\Controllers\Api\DashboardOwnerController::class, 'donasiHunterIndex'])
    ->name('dashboard.owner.donasi-hunter.index');

// Add the route for printing the donation hunter report
Route::get('/dashboard/owner/donasi-hunter/print', [App\Http\Controllers\Api\DashboardOwnerController::class, 'printDonasiHunterReport'])
    ->name('dashboard.owner.donasi-hunter.print');

// ========================================
// FALLBACK ROUTE
// ========================================

Route::fallback(function () {
    return view('errors.404');
});
