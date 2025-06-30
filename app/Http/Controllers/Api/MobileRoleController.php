<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobileRoleController extends Controller
{
    /**
     * Get role-based configuration for mobile app
     */
    public function getRoleConfig(Request $request)
    {
        try {
            $user = $request->user();
            $roleName = strtolower(trim($user->role->nama_role));
            
            $config = [
                'role' => $roleName,
                'theme' => $this->getRoleTheme($roleName),
                'features' => $this->getRoleFeatures($roleName),
                'restrictions' => $this->getRoleRestrictions($roleName),
                'default_route' => $this->getDefaultRoute($roleName),
                'notification_settings' => $this->getNotificationSettings($roleName)
            ];
            
            return response()->json([
                'success' => true,
                'data' => $config
            ], 200);
        } catch (\Exception $e) {
            Log::error('Role config error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil konfigurasi role'
            ], 500);
        }
    }
    
    /**
     * Get theme configuration based on role
     */
    private function getRoleTheme($roleName)
    {
        $themes = [
            'pembeli' => [
                'primary_color' => '#2196F3',
                'secondary_color' => '#FFC107',
                'accent_color' => '#4CAF50',
                'background_color' => '#FAFAFA',
                'card_color' => '#FFFFFF'
            ],
            'penitip' => [
                'primary_color' => '#FF9800',
                'secondary_color' => '#795548',
                'accent_color' => '#4CAF50',
                'background_color' => '#FFF8E1',
                'card_color' => '#FFFFFF'
            ],
            'admin' => [
                'primary_color' => '#9C27B0',
                'secondary_color' => '#673AB7',
                'accent_color' => '#E91E63',
                'background_color' => '#F3E5F5',
                'card_color' => '#FFFFFF'
            ],
            'owner' => [
                'primary_color' => '#1A237E',
                'secondary_color' => '#3F51B5',
                'accent_color' => '#FF5722',
                'background_color' => '#E8EAF6',
                'card_color' => '#FFFFFF'
            ],
            'gudang' => [
                'primary_color' => '#607D8B',
                'secondary_color' => '#455A64',
                'accent_color' => '#FF9800',
                'background_color' => '#ECEFF1',
                'card_color' => '#FFFFFF'
            ],
            'kurir' => [
                'primary_color' => '#4CAF50',
                'secondary_color' => '#388E3C',
                'accent_color' => '#FFC107',
                'background_color' => '#E8F5E8',
                'card_color' => '#FFFFFF'
            ]
        ];
        
        return $themes[$roleName] ?? $themes['pembeli'];
    }
    
    /**
     * Get role-specific features
     */
    private function getRoleFeatures($roleName)
    {
        $features = [
            'pembeli' => [
                'barcode_scanner' => true,
                'wishlist' => true,
                'loyalty_program' => true,
                'product_reviews' => true,
                'order_tracking' => true,
                'payment_gateway' => true,
                'address_management' => true,
                'customer_support_chat' => true
            ],
            'penitip' => [
                'item_upload' => false, // Usually web-only
                'commission_tracking' => true,
                'pickup_scheduling' => true,
                'item_status_tracking' => true,
                'sales_notifications' => true,
                'consignment_extension' => true
            ],
            'admin' => [
                'user_management' => true,
                'product_approval' => true,
                'transaction_monitoring' => true,
                'report_generation' => true,
                'system_notifications' => true,
                'bulk_operations' => true
            ],
            'gudang' => [
                'barcode_scanner' => true,
                'inventory_management' => true,
                'item_verification' => true,
                'stock_alerts' => true,
                'shipment_processing' => true,
                'warehouse_reports' => true
            ],
            'kurir' => [
                'gps_tracking' => true,
                'route_optimization' => true,
                'delivery_confirmation' => true,
                'photo_capture' => true,
                'customer_communication' => true,
                'offline_mode' => true
            ]
        ];
        
        return $features[$roleName] ?? [];
    }
    
    /**
     * Get role-specific restrictions
     */
    private function getRoleRestrictions($roleName)
    {
        $restrictions = [
            'pembeli' => [
                'max_cart_items' => 50,
                'max_addresses' => 10,
                'can_delete_account' => true,
                'can_change_email' => true
            ],
            'penitip' => [
                'max_active_items' => 100,
                'can_delete_account' => false,
                'can_change_email' => false,
                'requires_verification' => true
            ],
            'admin' => [
                'can_delete_users' => true,
                'can_modify_transactions' => true,
                'can_access_reports' => true,
                'session_timeout' => 480 // 8 hours
            ],
            'gudang' => [
                'can_modify_inventory' => true,
                'requires_barcode_scan' => true,
                'location_tracking' => true,
                'session_timeout' => 600 // 10 hours
            ]
        ];
        
        return $restrictions[$roleName] ?? [];
    }
    
    /**
     * Get default route for role
     */
    private function getDefaultRoute($roleName)
    {
        $routes = [
            'pembeli' => '/mobile/home',
            'penitip' => '/mobile/dashboard',
            'admin' => '/mobile/admin/dashboard',
            'owner' => '/mobile/owner/dashboard',
            'gudang' => '/mobile/warehouse/inventory',
            'kurir' => '/mobile/courier/deliveries',
            'hunter' => '/mobile/hunter/pickup-requests',
            'cs' => '/mobile/cs/support',
            'organisasi' => '/mobile/organization/dashboard'
        ];
        
        return $routes[$roleName] ?? '/mobile/dashboard';
    }
    
    /**
     * Get notification settings for role
     */
    private function getNotificationSettings($roleName)
    {
        $settings = [
            'pembeli' => [
                'order_updates' => true,
                'promotional_offers' => true,
                'loyalty_rewards' => true,
                'new_products' => false,
                'price_drops' => true
            ],
            'penitip' => [
                'item_sold' => true,
                'pickup_scheduled' => true,
                'commission_earned' => true,
                'item_expired' => true,
                'verification_updates' => true
            ],
            'admin' => [
                'new_registrations' => true,
                'system_alerts' => true,
                'transaction_anomalies' => true,
                'daily_reports' => true,
                'user_complaints' => true
            ],
            'gudang' => [
                'new_items_arrived' => true,
                'verification_required' => true,
                'shipment_ready' => true,
                'stock_alerts' => true,
                'system_maintenance' => true
            ],
            'kurir' => [
                'new_deliveries' => true,
                'route_updates' => true,
                'delivery_reminders' => true,
                'emergency_alerts' => true,
                'schedule_changes' => true
            ]
        ];
        
        return $settings[$roleName] ?? [];
    }
    
    /**
     * Update user's notification preferences
     */
    public function updateNotificationSettings(Request $request)
    {
        try {
            $user = $request->user();
            $settings = $request->input('notification_settings', []);
            
            // Here you would typically save to a user_settings table
            // For now, we'll just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan notifikasi berhasil diperbarui',
                'data' => [
                    'notification_settings' => $settings
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Update notification settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pengaturan'
            ], 500);
        }
    }
}
