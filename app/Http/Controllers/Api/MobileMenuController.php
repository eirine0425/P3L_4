<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MobileMenuController extends Controller
{
    /**
     * Get user permissions for mobile app
     */
    public function getUserPermissions(Request $request)
    {
        try {
            $user = $request->user();
            $roleName = strtolower(trim($user->role->nama_role));
            
            $permissions = $this->getRolePermissions($roleName);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'role' => $roleName,
                    'permissions' => $permissions,
                    'features' => $this->getRoleFeatures($roleName)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Mobile permissions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil permissions'
            ], 500);
        }
    }
    
    /**
     * Get role-specific permissions
     */
    private function getRolePermissions($roleName)
    {
        $permissions = [
            'pembeli' => [
                'can_view_products' => true,
                'can_add_to_cart' => true,
                'can_checkout' => true,
                'can_view_transactions' => true,
                'can_manage_addresses' => true,
                'can_view_loyalty_points' => true,
                'can_rate_products' => true,
                'can_contact_cs' => true
            ],
            'penitip' => [
                'can_view_my_items' => true,
                'can_view_transactions' => true,
                'can_request_pickup' => true,
                'can_extend_consignment' => true,
                'can_view_commission' => true,
                'can_upload_items' => false, // Usually done through web
                'can_contact_cs' => true
            ],
            'penjual' => [
                'can_view_my_items' => true,
                'can_view_transactions' => true,
                'can_request_pickup' => true,
                'can_extend_consignment' => true,
                'can_view_commission' => true,
                'can_upload_items' => false,
                'can_contact_cs' => true
            ],
            'admin' => [
                'can_manage_users' => true,
                'can_manage_consignors' => true,
                'can_manage_products' => true,
                'can_view_all_transactions' => true,
                'can_generate_reports' => true,
                'can_manage_categories' => true,
                'can_manage_system_settings' => true,
                'can_approve_registrations' => true
            ],
            'owner' => [
                'can_view_analytics' => true,
                'can_view_financial_reports' => true,
                'can_manage_employees' => true,
                'can_manage_business_settings' => true,
                'can_view_all_data' => true,
                'can_export_data' => true,
                'can_manage_system_config' => true
            ],
            'cs' => [
                'can_handle_customer_support' => true,
                'can_view_customer_data' => true,
                'can_process_complaints' => true,
                'can_chat_with_customers' => true,
                'can_view_transactions' => true,
                'can_assist_checkout' => true,
                'can_manage_tickets' => true
            ],
            'gudang' => [
                'can_manage_inventory' => true,
                'can_verify_items' => true,
                'can_process_shipments' => true,
                'can_manage_stock' => true,
                'can_scan_barcodes' => true,
                'can_update_item_status' => true,
                'can_generate_inventory_reports' => true
            ],
            'pegawai' => [
                'can_manage_inventory' => true,
                'can_verify_items' => true,
                'can_process_shipments' => true,
                'can_scan_barcodes' => true,
                'can_update_item_status' => true
            ],
            'kurir' => [
                'can_view_deliveries' => true,
                'can_update_delivery_status' => true,
                'can_scan_packages' => true,
                'can_take_delivery_photos' => true,
                'can_collect_payments' => true,
                'can_view_delivery_routes' => true,
                'can_contact_customers' => true
            ],
            'hunter' => [
                'can_view_pickup_requests' => true,
                'can_accept_pickup_jobs' => true,
                'can_update_pickup_status' => true,
                'can_take_pickup_photos' => true,
                'can_view_commission' => true,
                'can_navigate_to_pickup_location' => true,
                'can_contact_consignors' => true
            ],
            'organisasi' => [
                'can_create_donation_requests' => true,
                'can_view_received_donations' => true,
                'can_manage_organization_profile' => true,
                'can_generate_donation_reports' => true,
                'can_thank_donors' => true,
                'can_update_donation_status' => true
            ]
        ];
        
        return $permissions[$roleName] ?? [];
    }
    
    /**
     * Get role-specific features
     */
    private function getRoleFeatures($roleName)
    {
        $features = [
            'pembeli' => [
                'shopping_cart',
                'product_search',
                'order_tracking',
                'loyalty_program',
                'address_management',
                'payment_methods',
                'product_reviews'
            ],
            'penitip' => [
                'item_tracking',
                'commission_tracking',
                'pickup_scheduling',
                'consignment_extension',
                'sales_notifications'
            ],
            'penjual' => [
                'item_tracking',
                'commission_tracking',
                'pickup_scheduling',
                'consignment_extension',
                'sales_notifications'
            ],
            'admin' => [
                'user_management',
                'product_management',
                'transaction_monitoring',
                'report_generation',
                'system_configuration',
                'approval_workflows'
            ],
            'owner' => [
                'business_analytics',
                'financial_dashboard',
                'employee_management',
                'performance_metrics',
                'data_export',
                'strategic_reports'
            ],
            'cs' => [
                'customer_chat',
                'ticket_management',
                'complaint_handling',
                'customer_assistance',
                'transaction_support'
            ],
            'gudang' => [
                'inventory_management',
                'barcode_scanning',
                'item_verification',
                'shipment_processing',
                'stock_tracking',
                'warehouse_reports'
            ],
            'pegawai' => [
                'inventory_management',
                'barcode_scanning',
                'item_verification',
                'shipment_processing'
            ],
            'kurir' => [
                'delivery_tracking',
                'route_optimization',
                'package_scanning',
                'delivery_confirmation',
                'customer_communication',
                'payment_collection'
            ],
            'hunter' => [
                'pickup_job_board',
                'location_navigation',
                'pickup_confirmation',
                'photo_documentation',
                'commission_tracking'
            ],
            'organisasi' => [
                'donation_management',
                'donor_communication',
                'impact_reporting',
                'organization_profile',
                'donation_tracking'
            ]
        ];
        
        return $features[$roleName] ?? [];
    }
    
    /**
     * Check if user has specific permission
     */
    public function checkPermission(Request $request)
    {
        try {
            $user = $request->user();
            $roleName = strtolower(trim($user->role->nama_role));
            $permission = $request->input('permission');
            
            $permissions = $this->getRolePermissions($roleName);
            $hasPermission = isset($permissions[$permission]) && $permissions[$permission];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'has_permission' => $hasPermission,
                    'permission' => $permission,
                    'role' => $roleName
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Permission check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa permission'
            ], 500);
        }
    }
    
    /**
     * Get navigation structure for mobile app
     */
    public function getNavigationStructure(Request $request)
    {
        try {
            $user = $request->user();
            $roleName = strtolower(trim($user->role->nama_role));
            
            $navigation = [
                'bottom_navigation' => $this->getBottomNavigation($roleName),
                'drawer_navigation' => $this->getDrawerNavigation($roleName),
                'quick_actions' => $this->getQuickActions($roleName)
            ];
            
            return response()->json([
                'success' => true,
                'data' => $navigation
            ], 200);
        } catch (\Exception $e) {
            Log::error('Navigation structure error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil struktur navigasi'
            ], 500);
        }
    }
    
    /**
     * Get bottom navigation items
     */
    private function getBottomNavigation($roleName)
    {
        $bottomNav = [
            'pembeli' => [
                ['id' => 'home', 'title' => 'Beranda', 'icon' => 'home', 'route' => '/mobile/home'],
                ['id' => 'products', 'title' => 'Produk', 'icon' => 'shopping_bag', 'route' => '/mobile/products'],
                ['id' => 'cart', 'title' => 'Keranjang', 'icon' => 'shopping_cart', 'route' => '/mobile/cart'],
                ['id' => 'transactions', 'title' => 'Transaksi', 'icon' => 'receipt', 'route' => '/mobile/buyer/transactions'],
                ['id' => 'profile', 'title' => 'Profil', 'icon' => 'person', 'route' => '/mobile/profile']
            ],
            'penitip' => [
                ['id' => 'dashboard', 'title' => 'Dashboard', 'icon' => 'dashboard', 'route' => '/mobile/dashboard'],
                ['id' => 'items', 'title' => 'Barang', 'icon' => 'inventory', 'route' => '/mobile/consignor/items'],
                ['id' => 'transactions', 'title' => 'Transaksi', 'icon' => 'receipt', 'route' => '/mobile/consignor/transactions'],
                ['id' => 'pickup', 'title' => 'Pickup', 'icon' => 'local_shipping', 'route' => '/mobile/consignor/pickup'],
                ['id' => 'profile', 'title' => 'Profil', 'icon' => 'person', 'route' => '/mobile/profile']
            ],
            'admin' => [
                ['id' => 'dashboard', 'title' => 'Dashboard', 'icon' => 'dashboard', 'route' => '/mobile/dashboard'],
                ['id' => 'users', 'title' => 'Users', 'icon' => 'people', 'route' => '/mobile/admin/users'],
                ['id' => 'products', 'title' => 'Produk', 'icon' => 'inventory_2', 'route' => '/mobile/admin/products'],
                ['id' => 'reports', 'title' => 'Laporan', 'icon' => 'assessment', 'route' => '/mobile/admin/reports'],
                ['id' => 'profile', 'title' => 'Profil', 'icon' => 'person', 'route' => '/mobile/profile']
            ]
        ];
        
        return $bottomNav[$roleName] ?? $bottomNav['pembeli'];
    }
    
    /**
     * Get drawer navigation items
     */
    private function getDrawerNavigation($roleName)
    {
        // Create instance of AuthController to access the method
        $authController = new AuthController();
        $menu = $authController->generateMobileMenu($roleName);
        return $this->addBadgeCounts($menu, $roleName);
    }

    /**
     * Add badge counts to menu items
     */
    private function addBadgeCounts($menu, $roleName)
    {
        try {
            $user = request()->user(); // Gunakan request()->user() instead of auth()->user()
            
            foreach ($menu as &$item) {
                if (isset($item['badge'])) {
                    switch ($item['badge']) {
                        case 'cart_count':
                            if ($roleName === 'pembeli') {
                                $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
                                if ($pembeli) {
                                    $item['badge_count'] = \App\Models\KeranjangBelanja::where('pembeli_id', $pembeli->pembeli_id)->count();
                                }
                            }
                            break;
                    
                        case 'loyalty_points':
                            if ($roleName === 'pembeli') {
                                $pembeli = \App\Models\Pembeli::where('user_id', $user->id)->first();
                                if ($pembeli) {
                                    $item['badge_count'] = $pembeli->poin_loyalitas ?? 0;
                                }
                            }
                            break;
                    
                        case 'total_items':
                            if (in_array($roleName, ['penitip', 'penjual'])) {
                                $penitip = \App\Models\Penitip::where('user_id', $user->id)->first();
                                if ($penitip) {
                                    $item['badge_count'] = \App\Models\Barang::where('penitip_id', $penitip->penitip_id)->count();
                                }
                            }
                            break;
                    
                        case 'pending_verification':
                            if (in_array($roleName, ['gudang', 'pegawai'])) {
                                $item['badge_count'] = \App\Models\Barang::where('status_barang', 'Pending')->count();
                            }
                            break;
                    
                        case 'ready_to_ship':
                            if (in_array($roleName, ['gudang', 'pegawai'])) {
                                $item['badge_count'] = \App\Models\Transaksi::where('status_transaksi', 'Siap Kirim')->count();
                            }
                            break;
                    
                        case 'pending_deliveries':
                            if ($roleName === 'kurir') {
                                $item['badge_count'] = \App\Models\Pengiriman::where('status_pengiriman', 'Dalam Perjalanan')->count();
                            }
                            break;
                    
                        case 'available_pickups':
                            if ($roleName === 'hunter') {
                                $item['badge_count'] = \App\Models\TransaksiPenitipan::where('status_penjemputan', 'Menunggu Penjemputan')->count();
                            }
                            break;
                    
                        case 'new_donations':
                            if ($roleName === 'organisasi') {
                                $organisasi = \App\Models\Organisasi::where('user_id', $user->id)->first();
                                if ($organisasi) {
                                    $item['badge_count'] = \App\Models\Donasi::where('organisasi_id', $organisasi->organisasi_id)
                                        ->where('status_donasi', 'Baru')->count();
                                }
                            }
                            break;
                    
                        default:
                            $item['badge_count'] = 0;
                            break;
                    }
                }
            }
        
            return $menu;
        } catch (\Exception $e) {
            Log::error('Badge count error: ' . $e->getMessage());
            return $menu;
        }
    }
    
    /**
     * Get quick action buttons
     */
    private function getQuickActions($roleName)
    {
        $quickActions = [
            'pembeli' => [
                ['id' => 'scan_product', 'title' => 'Scan Produk', 'icon' => 'qr_code_scanner', 'action' => 'scan_barcode'],
                ['id' => 'search', 'title' => 'Cari Produk', 'icon' => 'search', 'action' => 'open_search'],
                ['id' => 'wishlist', 'title' => 'Wishlist', 'icon' => 'favorite', 'route' => '/mobile/wishlist'],
                ['id' => 'help', 'title' => 'Bantuan', 'icon' => 'help', 'action' => 'open_help']
            ],
            'gudang' => [
                ['id' => 'scan_item', 'title' => 'Scan Barang', 'icon' => 'qr_code_scanner', 'action' => 'scan_barcode'],
                ['id' => 'quick_verify', 'title' => 'Verifikasi Cepat', 'icon' => 'verified', 'action' => 'quick_verify'],
                ['id' => 'stock_check', 'title' => 'Cek Stok', 'icon' => 'inventory', 'action' => 'stock_check']
            ],
            'kurir' => [
                ['id' => 'scan_package', 'title' => 'Scan Paket', 'icon' => 'qr_code_scanner', 'action' => 'scan_package'],
                ['id' => 'navigation', 'title' => 'Navigasi', 'icon' => 'navigation', 'action' => 'open_navigation'],
                ['id' => 'emergency', 'title' => 'Darurat', 'icon' => 'emergency', 'action' => 'emergency_contact']
            ]
        ];
        
        return $quickActions[$roleName] ?? [];
    }
}
