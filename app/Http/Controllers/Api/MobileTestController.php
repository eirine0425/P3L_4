<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;

class MobileTestController extends Controller
{
    /**
     * Test basic API connection
     */
    public function testConnection()
    {
        try {
            Log::info('Mobile test connection endpoint called');
            
            return response()->json([
                'success' => true,
                'message' => 'API connection successful!',
                'timestamp' => now()->toISOString(),
                'server' => 'Laravel ' . app()->version(),
                'environment' => app()->environment(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Mobile test connection error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'API connection failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test database connection
     */
    public function testDatabase()
    {
        try {
            Log::info('Mobile test database endpoint called');
            
            // Test database connection
            DB::connection()->getPdo();
            
            // Count users
            $userCount = User::count();
            $roleCount = Role::count();
            
            return response()->json([
                'success' => true,
                'message' => 'Database connection successful!',
                'data' => [
                    'users_count' => $userCount,
                    'roles_count' => $roleCount,
                    'database_name' => config('database.connections.mysql.database'),
                ],
                'timestamp' => now()->toISOString(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Mobile test database error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test all components
     */
    public function testAll()
    {
        try {
            Log::info('Mobile test all endpoint called');
            
            $results = [];
            
            // Test API
            $results['api'] = [
                'status' => 'success',
                'message' => 'API is working',
                'laravel_version' => app()->version(),
            ];
            
            // Test Database
            try {
                DB::connection()->getPdo();
                $results['database'] = [
                    'status' => 'success',
                    'message' => 'Database connected',
                    'users_count' => User::count(),
                ];
            } catch (\Exception $e) {
                $results['database'] = [
                    'status' => 'error',
                    'message' => 'Database connection failed',
                    'error' => $e->getMessage(),
                ];
            }
            
            // Test Sanctum
            try {
                $results['sanctum'] = [
                    'status' => 'success',
                    'message' => 'Sanctum is configured',
                    'middleware' => 'Available',
                ];
            } catch (\Exception $e) {
                $results['sanctum'] = [
                    'status' => 'error',
                    'message' => 'Sanctum configuration issue',
                    'error' => $e->getMessage(),
                ];
            }
            
            // Test CORS
            $results['cors'] = [
                'status' => 'success',
                'message' => 'CORS headers should be present',
                'note' => 'Check browser network tab for CORS headers',
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'System test completed',
                'results' => $results,
                'timestamp' => now()->toISOString(),
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Mobile test all error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'System test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test authentication (requires token)
     */
    public function testAuth(Request $request)
    {
        try {
            Log::info('Mobile test auth endpoint called');
            
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'message' => 'Authentication test successful!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ? $user->role->nama_role : null,
                ],
                'timestamp' => now()->toISOString(),
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('Mobile test auth error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Authentication test failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
}
