<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Barang\BarangUseCase;
use App\DTOs\Barang\GetBarangPaginationRequest;
use App\Repositories\Interfaces\BarangRepositoryInterface;
use Illuminate\Http\Request;

class PenitipBarangController extends Controller
{
    public function __construct(
        protected BarangUseCase $barangUseCase,
        protected BarangRepositoryInterface $barangRepository
    ) {}

    /**
     * Get all items for a specific penitip with search and status filter
     * Endpoint: GET /api/penitip/{penitipId}/barang
     */
    public function index($penitipId, GetBarangPaginationRequest $request)
    {
        try {
            $result = $this->barangUseCase->searchByPenitip($penitipId, $request);
            $statusCounts = $this->barangRepository->getStatusCounts($penitipId);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'status_counts' => $statusCounts,
                'filters' => $request->getFilters(),
                'message' => 'Items retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search items with simple filters (nama barang + status)
     * Endpoint: POST /api/barang/search
     */
    public function search(GetBarangPaginationRequest $request)
    {
        try {
            $result = $this->barangUseCase->getAdvancedSearch($request);
            $statusCounts = $this->barangRepository->getStatusCounts();
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'status_counts' => $statusCounts,
                'filters' => $request->getFilters(),
                'message' => 'Search completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing search',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get status options for dropdown
     * Endpoint: GET /api/barang/status-options
     */
    public function getStatusOptions()
    {
        $statusOptions = [
            [
                'value' => 'semua_status',
                'label' => 'Semua Status',
                'selected' => true
            ],
            [
                'value' => 'menunggu_verifikasi',
                'label' => 'Menunggu Verifikasi',
                'selected' => false
            ],
            [
                'value' => 'aktif',
                'label' => 'Aktif',
                'selected' => false
            ],
            [
                'value' => 'tidak_aktif',
                'label' => 'Tidak Aktif',
                'selected' => false
            ],
            [
                'value' => 'terjual',
                'label' => 'Terjual',
                'selected' => false
            ],
            [
                'value' => 'ditolak',
                'label' => 'Ditolak',
                'selected' => false
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $statusOptions,
            'message' => 'Status options retrieved successfully'
        ]);
    }

    /**
     * Reset filters - return all items for penitip
     * Endpoint: POST /api/penitip/{penitipId}/barang/reset
     */
    public function resetFilters($penitipId, Request $request)
    {
        try {
            $resetRequest = new GetBarangPaginationRequest();
            $resetRequest->merge([
                'page' => 1,
                'per_page' => $request->input('per_page', 10)
            ]);
            
            $result = $this->barangUseCase->searchByPenitip($penitipId, $resetRequest);
            $statusCounts = $this->barangRepository->getStatusCounts($penitipId);
            
            return response()->json([
                'success' => true,
                'data' => $result,
                'status_counts' => $statusCounts,
                'message' => 'Filters reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting filters',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get item details
     * Endpoint: GET /api/barang/{id}
     */
    public function show($id)
    {
        try {
            $barang = $this->barangUseCase->find($id);
            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $barang,
                'message' => 'Item details retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving item details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
