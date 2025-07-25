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
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TransaksiMerchController;
use App\Http\Controllers\Api\TransaksiPenitipanController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\PenitipTransaksiController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\BuyerProfileController;
use App\Http\Controllers\Api\DashboardBuyerController;
use App\Http\Controllers\Api\BuyerTransactionController;
use App\Http\Controllers\Api\DashboardConsignorController;
use App\Http\Controllers\Api\DashboardAdminController;
use App\Http\Controllers\Api\DashboardWarehouseController;
use App\Http\Controllers\Api\DashboardCSController;
use App\Http\Controllers\Api\DashboardHunterController;
use App\Http\Controllers\Api\DashboardOrganisasiController;
use App\Http\Controllers\Api\DashboardOwnerController;
use App\Http\Controllers\Api\ConsignorPickupController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\PenitipBarangController;
use App\Http\Controllers\Api\UnsoldItemPickupController;
use App\Http\Controllers\Api\ConsignorPickupController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\MobileMenuController;
use App\Http\Controllers\Api\DashboardOwnerController;
use App\Http\Controllers\Dashboard\Owner\RequestDonasiController;

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

// ========================================
// PUBLIC ROUTES
// ========================================

// Authentication
// AUTHENTICATION ROUTES
// ========================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Public product routes
Route::get('/products', [WebViewController::class, 'products']);
Route::get('/products/{id}', [WebViewController::class, 'productDetail']);
Route::get('/categories', [KategoriBarangController::class, 'index']);

// Web view routes (public)
// ========================================
// OWNER DASHBOARD ROUTES
// ========================================

Route::get('/dashboard/owner/donasi-report', [DashboardOwnerController::class, 'donasiReport']);
Route::get('/dashboard/owner/donasi-hunter-report', [DashboardOwnerController::class, 'donasiHunterReport']);
Route::get('/dashboard/owner/donasi-hunter-export', [DashboardOwnerController::class, 'exportDonasiHunterReport']);

// Owner Dashboard API Routes
Route::middleware(['auth:api', 'role:owner'])->prefix('dashboard/owner')->group(function () {
    Route::get('/', [DashboardOwnerController::class, 'index']);
    Route::get('/donasi-report', [DashboardOwnerController::class, 'donasiReport']);
    Route::get('/donasi-export', [DashboardOwnerController::class, 'exportDonasiReport']);
    Route::get('/request-donasi', [DashboardOwnerController::class, 'requestDonasiReport']);
    Route::get('/request-donasi-export', [DashboardOwnerController::class, 'exportRequestDonasiReport']);
    
    // Transaksi Penitipan Reports
    Route::get('/transaksi-penitipan-report', [DashboardOwnerController::class, 'transaksiPenitipanReport']);
    Route::get('/transaksi-penitipan-export', [DashboardOwnerController::class, 'exportTransaksiPenitipanReport']);

    // Donation Hunter Reports
    Route::get('/donasi-hunter-report', [DashboardOwnerController::class, 'donasiHunterReport']);
    Route::get('/donasi-hunter-export', [DashboardOwnerController::class, 'exportDonasiHunterReport']);
});

// ========================================
// SANCTUM AUTHENTICATED ROUTES
// ========================================

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Penitip Transaction Management
    Route::get('/penitip/my-transaksi', [PenitipTransaksiController::class, 'myTransaksi']);
    Route::post('/penitip/extend-penitipan', [PenitipTransaksiController::class, 'extendMyPenitipan']);
    Route::post('/transaksi/extend', [PenitipTransaksiController::class, 'extendMyPenitipan']);
});

// ========================================
// MOBILE API ROUTES
// ========================================

Route::prefix('mobile')->group(function () {
    Route::post('/login', [AuthController::class, 'mobileLogin']);
    Route::post('/register', [AuthController::class, 'mobileRegister']);
    Route::get('/app-info', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => 'Reusemart Mobile',
                'version' => '1.0.0',
                'status' => 'active'
            ]
        ]);
    });
    
    // Protected Mobile Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'mobileLogout']);
        Route::get('/user', [AuthController::class, 'mobileUser']);
        Route::get('/dashboard', [AuthController::class, 'mobileDashboard']);
        Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        
        // Products
        Route::get('/products', [BarangController::class, 'mobileIndex']);
        Route::get('/products/{id}', [BarangController::class, 'mobileShow']);
        Route::get('/products/search/{keyword}', [BarangController::class, 'mobileSearch']);
        Route::get('/products/featured', [BarangController::class, 'mobileFeatured']);
        
        // Categories
        Route::get('/categories', [KategoriBarangController::class, 'mobileIndex']);
        Route::get('/categories/{id}/products', [KategoriBarangController::class, 'mobileProducts']);
        
        // Cart
        Route::get('/cart', [KeranjangBelanjaController::class, 'mobileIndex']);
        Route::post('/cart/add', [KeranjangBelanjaController::class, 'mobileAddToCart']);
        Route::put('/cart/{id}', [KeranjangBelanjaController::class, 'mobileUpdateCart']);
        Route::delete('/cart/{id}', [KeranjangBelanjaController::class, 'mobileRemoveFromCart']);
        Route::post('/cart/clear', [KeranjangBelanjaController::class, 'mobileClearCart']);
        Route::get('/cart/count', [KeranjangBelanjaController::class, 'mobileCartCount']);
        
        // Transactions
        Route::get('/transactions', [TransaksiController::class, 'mobileIndex']);
        Route::get('/transactions/{id}', [TransaksiController::class, 'mobileShow']);
        Route::post('/transactions/checkout', [TransaksiController::class, 'mobileCheckout']);
        
        // Address
        Route::get('/addresses', [AlamatController::class, 'mobileIndex']);
        Route::post('/addresses', [AlamatController::class, 'mobileStore']);
        Route::put('/addresses/{id}', [AlamatController::class, 'mobileUpdate']);
        Route::delete('/addresses/{id}', [AlamatController::class, 'mobileDestroy']);
        
        // Shipping
        Route::get('/shipping/options', [PengirimanController::class, 'mobileOptions']);
        Route::post('/shipping/calculate', [PengirimanController::class, 'mobileCalculate']);
    });
});

// ========================================
// MOBILE MENU AND PERMISSIONS ROUTES
// ========================================

// Mobile Menu Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mobile/menu', [AuthController::class, 'getMobileMenu']);
    Route::get('/mobile/permissions', [MobileMenuController::class, 'getUserPermissions']);
    Route::post('/mobile/check-permission', [MobileMenuController::class, 'checkPermission']);
    Route::get('/mobile/navigation', [MobileMenuController::class, 'getNavigationStructure']);
});

// ========================================
// MOBILE DASHBOARD ROUTES
// ========================================

Route::middleware(['auth:sanctum'])->prefix('mobile')->name('mobile.')->group(function () {
    // Dashboard data based on role
    Route::get('/dashboard', [AuthController::class, 'mobileDashboard']);
    
    // Menu based on role
    Route::get('/menu', [AuthController::class, 'getMobileMenu']);
    
    // Additional menu and permission routes
    Route::get('/permissions', [MobileMenuController::class, 'getUserPermissions']);
    Route::post('/check-permission', [MobileMenuController::class, 'checkPermission']);
    Route::get('/navigation', [MobileMenuController::class, 'getNavigationStructure']);
    
    // Profile management
    Route::get('/profile', [AuthController::class, 'mobileProfile']);
    Route::put('/profile', [AuthController::class, 'updateMobileProfile']);
    
    // Role-specific mobile routes
    Route::middleware('role:pembeli')->group(function () {
        Route::get('/buyer/dashboard', [DashboardBuyerController::class, 'mobileIndex']);
        Route::get('/buyer/transactions', [BuyerTransactionController::class, 'mobileIndex']);
        Route::get('/buyer/cart', [KeranjangBelanjaController::class, 'mobileCart']);
    });
    
    Route::middleware('role:penitip')->group(function () {
        Route::get('/consignor/dashboard', [DashboardConsignorController::class, 'mobileIndex']);
        Route::get('/consignor/items', [DashboardConsignorController::class, 'mobileItems']);
        Route::get('/consignor/transactions', [DashboardConsignorController::class, 'mobileTransactions']);
    });
    
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardAdminController::class, 'mobileIndex']);
        Route::get('/admin/users', [DashboardAdminController::class, 'mobileUsers']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// ========================================
// WEB VIEW ROUTES (PUBLIC)
// ========================================

Route::get('/beranda', [WebViewController::class, 'beranda']);
Route::get('/produk', [WebViewController::class, 'daftarProduk']);
Route::get('/produk/{id}', [WebViewController::class, 'tampilProduk']);
Route::get('/garansi/cek', [WebViewController::class, 'cekGaransi']);
Route::get('/tentang-kami', [WebViewController::class, 'tentangKami']);

// App info
Route::get('/app-info', function() {
    return response()->json([
        'success' => true,
        'data' => [
            'name' => 'Reusemart Mobile API',
            'version' => '1.0.0',
            'status' => 'active'
        ]
    ]);
});

// ========================================
// MOBILE API ROUTES
// ========================================

Route::prefix('mobile')->group(function () {
    Route::post('/login', [AuthController::class, 'mobileLogin']);
    Route::post('/register', [AuthController::class, 'mobileRegister']);
    Route::get('/app-info', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => 'Reusemart Mobile',
                'version' => '1.0.0',
                'status' => 'active'
            ]
        ]);
    });
    
    // Protected Mobile Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'mobileLogout']);
        Route::get('/user', [AuthController::class, 'mobileUser']);
        Route::get('/dashboard', [AuthController::class, 'mobileDashboard']);
        Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
        
        // Products
        Route::get('/products', [BarangController::class, 'mobileIndex']);
        Route::get('/products/{id}', [BarangController::class, 'mobileShow']);
        Route::get('/products/search/{keyword}', [BarangController::class, 'mobileSearch']);
        Route::get('/products/featured', [BarangController::class, 'mobileFeatured']);
        
        // Categories
        Route::get('/categories', [KategoriBarangController::class, 'mobileIndex']);
        Route::get('/categories/{id}/products', [KategoriBarangController::class, 'mobileProducts']);
        
        // Cart
        Route::get('/cart', [KeranjangBelanjaController::class, 'mobileIndex']);
        Route::post('/cart/add', [KeranjangBelanjaController::class, 'mobileAddToCart']);
        Route::put('/cart/{id}', [KeranjangBelanjaController::class, 'mobileUpdateCart']);
        Route::delete('/cart/{id}', [KeranjangBelanjaController::class, 'mobileRemoveFromCart']);
        Route::post('/cart/clear', [KeranjangBelanjaController::class, 'mobileClearCart']);
        Route::get('/cart/count', [KeranjangBelanjaController::class, 'mobileCartCount']);
        
        // Transactions
        Route::get('/transactions', [TransaksiController::class, 'mobileIndex']);
        Route::get('/transactions/{id}', [TransaksiController::class, 'mobileShow']);
        Route::post('/transactions/checkout', [TransaksiController::class, 'mobileCheckout']);
        
        // Address
        Route::get('/addresses', [AlamatController::class, 'mobileIndex']);
        Route::post('/addresses', [AlamatController::class, 'mobileStore']);
        Route::put('/addresses/{id}', [AlamatController::class, 'mobileUpdate']);
        Route::delete('/addresses/{id}', [AlamatController::class, 'mobileDestroy']);
        
        // Shipping
        Route::get('/shipping/options', [PengirimanController::class, 'mobileOptions']);
        Route::post('/shipping/calculate', [PengirimanController::class, 'mobileCalculate']);
    });
});

// ========================================
// SANCTUM AUTHENTICATED ROUTES
// ========================================

Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::get('/me', [AuthController::class, 'me']);

  

    // Penitip Transaction Management
    Route::get('/penitip/my-transaksi', [PenitipTransaksiController::class, 'myTransaksi']);
    Route::post('/penitip/extend-penitipan', [PenitipTransaksiController::class, 'extendMyPenitipan']);
    Route::post('/transaksi/extend', [PenitipTransaksiController::class, 'extendMyPenitipan']);

    // ========================================
    // DASHBOARD ROUTES (ROLE-BASED)
    // ========================================

    Route::prefix('dashboard')->group(function () {
        // Admin Dashboard
        Route::middleware('role:admin')->prefix('admin')->group(function () {
            Route::get('/', [DashboardAdminController::class, 'index']);
            Route::get('/users', [DashboardAdminController::class, 'users']);
            Route::get('/transactions', [DashboardAdminController::class, 'transactions']);
            Route::get('/reports', [DashboardAdminController::class, 'reports']);
        });

        // Owner Dashboard
        Route::middleware('role:owner')->prefix('owner')->group(function () {
            Route::get('/', [DashboardOwnerController::class, 'index']);
            Route::get('/sales-report', [DashboardOwnerController::class, 'salesReportByCategory']);
            Route::get('/expired-items', [DashboardOwnerController::class, 'expiredItems']);
            Route::get('/profit-report', [DashboardOwnerController::class, 'profitReport']);
            Route::get('/export-report', [DashboardOwnerController::class, 'exportReport']);
        });
        

        // Buyer Dashboard
        Route::middleware('role:pembeli')->prefix('buyer')->group(function () {
            Route::get('/', [DashboardBuyerController::class, 'index']);
            Route::get('/profile', [BuyerProfileController::class, 'show']);
            Route::put('/profile', [BuyerProfileController::class, 'update']);
            Route::get('/transactions', [BuyerTransactionController::class, 'index']);
            Route::get('/transactions/{id}', [BuyerTransactionController::class, 'show']);
        });

        // Consignor Dashboard
        Route::middleware('role:penitip')->prefix('consignor')->group(function () {
            Route::get('/', [DashboardConsignorController::class, 'index']);
            Route::get('/items', [PenitipBarangController::class, 'index']);
            Route::get('/items/{id}', [PenitipBarangController::class, 'show']);
            Route::get('/transactions', [PenitipTransaksiController::class, 'index']);
            Route::get('/pickup', [ConsignorPickupController::class, 'index']);
        });

        // CS Dashboard
        Route::middleware('role:cs')->prefix('cs')->group(function () {
            Route::get('/', [DashboardCSController::class, 'index']);
        });

        // Hunter Dashboard
        Route::middleware('role:hunter')->prefix('hunter')->group(function () {
            Route::get('/', [DashboardHunterController::class, 'index']);
        });

        // Organization Dashboard
        Route::middleware('role:organisasi')->prefix('organization')->group(function () {
            Route::get('/', [DashboardOrganisasiController::class, 'index']);
        });

        // Warehouse Dashboard
        Route::middleware('role:warehouse')->prefix('warehouse')->group(function () {
            Route::get('/', [DashboardWarehouseController::class, 'index']);
        });

        // Legacy dashboard routes (for backward compatibility)
        Route::get('/pemilik', [WebViewController::class, 'dashboardPemilik'])->middleware('role:owner');
        Route::get('/admin', [WebViewController::class, 'dashboardAdmin'])->middleware('role:admin');
        Route::get('/gudang', [WebViewController::class, 'dashboardGudang'])->middleware('role:pegawai gudang');
        Route::get('/cs', [WebViewController::class, 'dashboardCS'])->middleware('role:cs');
        Route::get('/penitip', [WebViewController::class, 'dashboardPenitip'])->middleware('role:penitip');
        Route::get('/pembeli', [WebViewController::class, 'dashboardPembeli'])->middleware('role:pembeli');
        Route::get('/organisasi', [WebViewController::class, 'dashboardOrganisasi'])->middleware('role:organisasi');
    });

    // ========================================
    // RATING SYSTEM ROUTES
    // ========================================

    // Core Rating CRUD
   
// ========================================
// CART & CHECKOUT ROUTES (AUTHENTICATED)
// ========================================

Route::middleware('auth:api')->group(function () {
    Route::get('/keranjang', [WebViewController::class, 'keranjang']);
    Route::post('/keranjang/tambah', [WebViewController::class, 'tambahKeKeranjang']);
    Route::post('/keranjang/hapus', [WebViewController::class, 'hapusDariKeranjang']);
    Route::get('/checkout', [WebViewController::class, 'checkout']);
    Route::post('/checkout/proses', [WebViewController::class, 'prosesCheckout']);
    Route::post('/checkout', [TransaksiController::class, 'store']);
});

// Shipping calculation routes
Route::middleware('auth:api')->group(function () {
    Route::post('/shipping/calculate', [TransaksiController::class, 'calculateShipping']);
    Route::get('/shipping/info', [TransaksiController::class, 'getShippingInfo']);
});

// Cart selected items route
Route::middleware('auth:api')->group(function () {
    Route::post('/cart/selected-items', [WebViewController::class, 'getSelectedCartItems']);
});

// ========================================
// DASHBOARD ROUTES (ROLE-BASED)
// ========================================

Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard/pemilik', [WebViewController::class, 'dashboardPemilik'])->middleware('role:owner');
    Route::get('/dashboard/admin', [WebViewController::class, 'dashboardAdmin'])->middleware('role:admin');
    Route::get('/dashboard/gudang', [WebViewController::class, 'dashboardGudang'])->middleware('role:pegawai gudang');
    Route::get('/dashboard/cs', [WebViewController::class, 'dashboardCS'])->middleware('role:cs');
    Route::get('/dashboard/penitip', [WebViewController::class, 'dashboardPenitip'])->middleware('role:penitip');
    Route::get('/dashboard/pembeli', [WebViewController::class, 'dashboardPembeli'])->middleware('role:pembeli');
    Route::get('/dashboard/organisasi', [WebViewController::class, 'dashboardOrganisasi'])->middleware('role:organisasi');
});

// Transaction status and auto-cancel routes
Route::get('/transactions/{id}/status', [WebViewController::class, 'checkTransactionStatus']);
Route::post('/transactions/{id}/auto-cancel', [WebViewController::class, 'autoCancelExpiredTransaction']);

Route::middleware('auth:api')->group(function () {
    Route::get('/buyer/alamat/default', [AlamatController::class, 'getDefault']);
    Route::get('/buyer/alamat/select', [AlamatController::class, 'getForSelection']);
});

// ========================================
// RATING SYSTEM ROUTES
// ========================================

Route::middleware(['auth:api'])->group(function () {
    // Core Rating CRUD
    Route::apiResource('ratings', RatingController::class);
    
    // Rating Query Routes
    Route::get('/ratings/item/{barang_id}', [RatingController::class, 'getItemRatings']);
    Route::get('/ratings/consignor/{penitip_id}', [RatingController::class, 'getConsignorRatings']);
    Route::get('/my-ratings', [RatingController::class, 'getMyRatings']);
    Route::get('/ratable-items', [RatingController::class, 'getRatableItems']);
    
    // Buyer Profile Rating Routes
    Route::prefix('buyer')->name('api.buyer.')->group(function () {
        Route::get('/ratings', [BuyerProfileController::class, 'showRatings']);
        Route::post('/ratings', [BuyerProfileController::class, 'submitRating']);
        Route::put('/ratings/{id}', [BuyerProfileController::class, 'updateRating']);
        Route::delete('/ratings/{id}', [BuyerProfileController::class, 'deleteRating']);
        Route::get('/rating-stats', [BuyerProfileController::class, 'getRatingStats']);
    });
    
    

    // ========================================
    // SHOPPING CART ROUTES
    // ========================================

    Route::prefix('keranjang-belanja')->name('api.cart.')->group(function () {
        Route::get('/', [KeranjangBelanjaController::class, 'index']);
        Route::post('/', [KeranjangBelanjaController::class, 'store']);
        Route::get('/{id}', [KeranjangBelanjaController::class, 'show']);
        Route::put('/{id}', [KeranjangBelanjaController::class, 'update']);
        Route::delete('/{id}', [KeranjangBelanjaController::class, 'destroy']);
        Route::post('/clear', [KeranjangBelanjaController::class, 'clearCart']);
    });

    // Legacy cart routes
    Route::get('/keranjang', [WebViewController::class, 'keranjang']);
    Route::post('/keranjang/tambah', [WebViewController::class, 'tambahKeKeranjang']);
    Route::post('/keranjang/hapus', [WebViewController::class, 'hapusDariKeranjang']);
    Route::post('/cart/add', [KeranjangBelanjaController::class, 'addToCart']);
    Route::delete('/cart/remove/{id}', [KeranjangBelanjaController::class, 'removeFromCart']);
    Route::put('/cart/update/{id}', [KeranjangBelanjaController::class, 'updateQuantity']);

    // ========================================
    // CHECKOUT ROUTES
    // ========================================

    Route::get('/checkout', [WebViewController::class, 'checkout']);
    Route::post('/checkout/proses', [WebViewController::class, 'prosesCheckout']);

    // ========================================
    // CORE RESOURCE ROUTES
    // ========================================

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('alamat', AlamatController::class);
    Route::apiResource('barang', BarangController::class);
    Route::apiResource('kategori-barang', KategoriBarangController::class);
    Route::apiResource('penitip', PenitipController::class);
    Route::apiResource('pembeli', PembeliController::class);
    Route::apiResource('transaksi', TransaksiController::class);
    Route::apiResource('detail-transaksi', DetailTransaksiController::class);
    Route::apiResource('keranjang-belanja', KeranjangBelanjaController::class);
    Route::apiResource('diskusi-produk', DiskusiProdukController::class);
    Route::apiResource('garansi', GaransiController::class);
    Route::apiResource('komisi', KomisiController::class);
    Route::apiResource('merch', MerchController::class);
    Route::apiResource('organisasi', OrganisasiController::class);
    Route::apiResource('pegawai', PegawaiController::class);
    Route::apiResource('pengiriman', PengirimanController::class);
    Route::apiResource('donasi', DonasiController::class);
    Route::apiResource('request-donasi', RequestDonasiController::class);
    Route::apiResource('transaksi-merch', TransaksiMerchController::class);
    Route::apiResource('transaksi-penitipan', TransaksiPenitipanController::class);

    // ========================================
    // ADDITIONAL BUSINESS LOGIC ROUTES
    // ========================================

    // Advanced search routes
    Route::get('/barang/search/penitip/{penitipId}', [BarangController::class, 'searchByPenitip']);
    Route::get('/barang/advanced-search', [BarangController::class, 'advancedSearch']);

    // Shipping & Logistics
    Route::get('/pengiriman/transaksi-siap', [PengirimanController::class, 'transaksiSiapKirim']);

    // Transaction extensions
    Route::post('/transaksi-penitipan/{id}/extend', [TransaksiPenitipanController::class, 'extendPenitipan']);

    // Pickup routes
    Route::get('/pickup/unsold-items', [UnsoldItemPickupController::class, 'index']);
    Route::post('/pickup/schedule', [UnsoldItemPickupController::class, 'schedule']);
    Route::put('/pickup/complete/{id}', [UnsoldItemPickupController::class, 'complete']);

    // ========================================
    // REPORTS & ANALYTICS ROUTES
    // ========================================

    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesReport']);
        Route::get('/inventory', [ReportController::class, 'inventoryReport']);
        Route::get('/consignment', [ReportController::class, 'consignmentReport']);
        
        // Expired items report routes
        Route::prefix('expired-items')->group(function () {
            Route::get('/filters', [DashboardOwnerController::class, 'getExpiredItemsFilters']);
            Route::get('/data', [DashboardOwnerController::class, 'getExpiredItemsData']);
            Route::get('/pdf', [DashboardOwnerController::class, 'expiredItemsPDF']);
        });
    });

    // Debug routes
    Route::get('/debug/barang', [DashboardOwnerController::class, 'getAllBarang']);
});

// ========================================
// JWT AUTHENTICATED ROUTES (Legacy)
// ========================================

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ========================================
// PDF DOWNLOAD ROUTES
// ========================================

// PDF Download routes (accessible with session auth)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/reports/expired-items/pdf', [DashboardOwnerController::class, 'expiredItemsPDF']);
    Route::get('/dashboard/owner/sales-report/pdf', [DashboardOwnerController::class, 'salesReportPDF']);
    Route::get('/dashboard/owner/expired-items/pdf', [DashboardOwnerController::class, 'expiredItemsPDF']);
   //oute::get('/dashboard/sales-report-pdf', [DashboardOwnerController::class, 'salesReportPDF']);
});

// ========================================
// DEBUG ROUTES (TEMPORARY)
// ========================================

Route::get('/debug/expired-items', [DashboardOwnerController::class, 'debugExpiredItems']);
Route::get('/debug/expired-items-public', [DashboardOwnerController::class, 'debugExpiredItems']);
    // Consignor Rating Routes
    Route::prefix('consignor')->name('api.consignor.')->group(function () {
        Route::get('/ratings', [RatingController::class, 'getConsignorReceivedRatings']);
        Route::get('/rating-summary', [RatingController::class, 'getConsignorRatingSummary']);
        Route::get('/rating-analytics', [RatingController::class, 'getConsignorRatingAnalytics']);
    });
});

// ========================================
// CORE RESOURCE ROUTES
// ========================================

// Alamat Management
Route::apiResource('alamat', AlamatController::class);

// Barang Management
Route::apiResource('barang', BarangController::class);

// Detail Transaksi Management
Route::apiResource('detail-transaksi', DetailTransaksiController::class);

// Diskusi Produk Management
Route::apiResource('diskusi-produk', DiskusiProdukController::class);

// Donasi Management
Route::apiResource('donasi', DonasiController::class);

// Garansi Management
Route::apiResource('garansi', GaransiController::class);

// Kategori Barang Management
Route::apiResource('kategori-barang', KategoriBarangController::class);

// ========================================
// SHOPPING CART ROUTES
// ========================================

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('keranjang-belanja')->name('api.cart.')->group(function () {
        Route::get('/', [KeranjangBelanjaController::class, 'index']);
        Route::post('/', [KeranjangBelanjaController::class, 'store']);
        Route::get('/{id}', [KeranjangBelanjaController::class, 'show']);
        Route::put('/{id}', [KeranjangBelanjaController::class, 'update']);
        Route::delete('/{id}', [KeranjangBelanjaController::class, 'destroy']);
        Route::post('/clear', [KeranjangBelanjaController::class, 'clearCart']);
    });
});

// ========================================
// SHIPPING & LOGISTICS ROUTES
// ========================================

Route::get('/pengiriman/transaksi-siap', [PengirimanController::class, 'transaksiSiapKirim']);
Route::apiResource('pengiriman', PengirimanController::class);

// ========================================
// BUSINESS LOGIC ROUTES
// ========================================

// Komisi Management
Route::apiResource('komisi', KomisiController::class);

// Merchandise Management
Route::apiResource('merch', MerchController::class);

// Organization Management
Route::apiResource('organisasi', OrganisasiController::class);

// Employee Management
Route::apiResource('pegawai', PegawaiController::class);

// Buyer Management
Route::apiResource('pembeli', PembeliController::class);

// Consignor Management
Route::apiResource('penitip', PenitipController::class);

// Donation Request Management
Route::apiResource('request-donasi', RequestDonasiController::class);

// Role Management
Route::apiResource('role', RoleController::class);

// ========================================
// TRANSACTION ROUTES
// ========================================

// Main Transactions
Route::apiResource('transaksi', TransaksiController::class);

// Merchandise Transactions
Route::apiResource('transaksi-merch', TransaksiMerchController::class);

// Consignment Transactions
Route::apiResource('transaksi-penitipan', TransaksiPenitipanController::class);
Route::post('/transaksi-penitipan/{id}/extend', [TransaksiPenitipanController::class, 'extendPenitipan']);

// ========================================
// USER MANAGEMENT ROUTES
// ========================================

Route::apiResource('users', UserController::class);

// ========================================
// APP INFO ROUTES
// ========================================

Route::get('/app-info', function() {
    return response()->json([
        'success' => true,
        'data' => [
            'name' => 'Reusemart Mobile API',
            'version' => '1.0.0',
            'status' => 'active'
        ]
    ]);
});

// ========================================
// FALLBACK ROUTE
// ========================================

Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API endpoint not found',
        'code' => 404
    ], 404);
});

