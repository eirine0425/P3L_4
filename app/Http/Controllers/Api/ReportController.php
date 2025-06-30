<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Penitip;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Get expired items data for API
     */
    public function expiredItemsData(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $penitipId = $request->input('penitip_id');
            $kategoriId = $request->input('kategori_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        
            // Debug: Log the request parameters
            \Log::info('Expired items request parameters:', [
                'search' => $search,
                'penitip_id' => $penitipId,
                'kategori_id' => $kategoriId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        
            $query = Barang::with(['penitip.user', 'kategoriBarang'])
                ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                ->where('status', '!=', 'diambil_kembali')
                ->where('status', '!=', 'terjual');
        
            // Apply filters
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhereHas('penitip', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('penitip.user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategoriBarang', function($q) use ($search) {
                          $q->where('nama_kategori', 'like', "%{$search}%");
                      });
                });
            }
        
            if ($penitipId) {
                $query->where('penitip_id', $penitipId);
            }
        
            if ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            }
        
            if ($startDate && $endDate) {
                $query->whereBetween('batas_penitipan', [$startDate, $endDate]);
            }
        
            // Debug: Log the SQL query
            \Log::info('SQL Query:', ['query' => $query->toSql(), 'bindings' => $query->getBindings()]);
        
            $expiredItems = $query->orderBy('batas_penitipan', 'asc')->paginate(50);

            // Add calculated fields
            $expiredItems->getCollection()->transform(function ($item) {
                $item->sisa_hari = Carbon::now()->diffInDays(Carbon::parse($item->batas_penitipan), false);
                return $item;
            });

            // Calculate summary for all items (not just paginated)
            $totalQuery = Barang::whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                ->where('status', '!=', 'diambil_kembali')
                ->where('status', '!=', 'terjual');
            
            if ($search) {
                $totalQuery->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhereHas('penitip', function($q) use ($search) {
                          $q->where('nama', 'like', "%{$search}%");
                      })
                      ->orWhereHas('penitip.user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategoriBarang', function($q) use ($search) {
                          $q->where('nama_kategori', 'like', "%{$search}%");
                      });
                });
            }
        
            if ($penitipId) {
                $totalQuery->where('penitip_id', $penitipId);
            }
        
            if ($kategoriId) {
                $totalQuery->where('kategori_id', $kategoriId);
            }
        
            if ($startDate && $endDate) {
                $totalQuery->whereBetween('batas_penitipan', [$startDate, $endDate]);
            }
        
            $totalItems = $totalQuery->count();
            $totalValue = $totalQuery->sum('harga');
            $avgDaysExpired = $totalQuery->selectRaw('AVG(DATEDIFF(CURDATE(), batas_penitipan)) as avg_days')->first()->avg_days ?? 0;

            // Debug: Log the results
            \Log::info('Query results:', [
                'total_items' => $totalItems,
                'total_value' => $totalValue,
                'avg_days_expired' => $avgDaysExpired,
                'paginated_count' => $expiredItems->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $expiredItems,
                'summary' => [
                    'total_expired_items' => $totalItems,
                    'total_value' => $totalValue,
                    'average_days_expired' => round($avgDaysExpired, 1)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in expiredItemsData:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Error loading expired items data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filter options for expired items report
     */
    public function getFilterOptions()
    {
        try {
            // Debug: Log that this method is called
            \Log::info('getFilterOptions called');
        
            $penitips = Penitip::with('user')
                ->whereHas('barang', function($query) {
                    $query->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                          ->where('status', '!=', 'diambil_kembali')
                          ->where('status', '!=', 'terjual');
                })
                ->get()
                ->map(function($penitip) {
                    return [
                        'id' => $penitip->penitip_id,
                        'name' => $penitip->user->name ?? $penitip->nama ?? 'Unknown'
                    ];
                });

            $categories = KategoriBarang::whereHas('barang', function($query) {
                $query->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                      ->where('status', '!=', 'diambil_kembali')
                      ->where('status', '!=', 'terjual');
            })
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->kategori_id,
                    'name' => $category->nama_kategori
                ];
            });

            // Debug: Log the results
            \Log::info('Filter options results:', [
                'penitips_count' => $penitips->count(),
                'categories_count' => $categories->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'penitips' => $penitips,
                    'categories' => $categories
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getFilterOptions:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        
            return response()->json([
                'success' => false,
                'message' => 'Error loading filter options: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download expired items PDF report
     */
    public function expiredItemsReport(Request $request)
    {
        try {
            $search = $request->input('search', '');
            $penitipId = $request->input('penitip_id');
            $kategoriId = $request->input('kategori_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $query = Barang::with(['penitip.user', 'kategoriBarang'])
                ->whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                ->where('status', '!=', 'diambil_kembali')
                ->where('status', '!=', 'terjual');
            
            // Apply same filters as data method
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                      ->orWhereHas('penitip.user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('kategoriBarang', function($q) use ($search) {
                          $q->where('nama_kategori', 'like', "%{$search}%");
                      });
                });
            }
            
            if ($penitipId) {
                $query->where('penitip_id', $penitipId);
            }
            
            if ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            }
            
            if ($startDate && $endDate) {
                $query->whereBetween('batas_penitipan', [$startDate, $endDate]);
            }
            
            $expiredItems = $query->orderBy('batas_penitipan', 'asc')->get();

            // Add calculated fields
            $expiredItems->transform(function ($item) {
                $item->sisa_hari = Carbon::now()->diffInDays(Carbon::parse($item->batas_penitipan), false);
                return $item;
            });

            // Calculate summaries
            $totalItems = $expiredItems->count();
            $totalValue = $expiredItems->sum('harga');
            $avgDaysExpired = $expiredItems->avg(function($item) {
                return abs($item->sisa_hari);
            });

            // Group by category
            $byCategory = $expiredItems->groupBy('kategoriBarang.nama_kategori')->map(function($items, $category) {
                return [
                    'category' => $category ?: 'Tidak Berkategori',
                    'count' => $items->count(),
                    'total_value' => $items->sum('harga')
                ];
            })->sortByDesc('count')->values();

            // Group by penitip
            $byPenitip = $expiredItems->groupBy(function($item) {
                return $item->penitip->user->name ?? $item->penitip->nama ?? 'Tidak Diketahui';
            })->map(function($items, $penitip) {
                return [
                    'penitip' => $penitip,
                    'count' => $items->count(),
                    'total_value' => $items->sum('harga')
                ];
            })->sortByDesc('count')->values();

            // Prepare data for PDF
            $data = [
                'title' => 'Laporan Barang Masa Penitipan Habis',
                'period' => $this->formatPeriod($startDate, $endDate),
                'expired_items' => $expiredItems,
                'by_category' => $byCategory,
                'by_penitip' => $byPenitip,
                'summary' => [
                    'total_expired_items' => $totalItems,
                    'total_value' => $totalValue,
                    'average_days_expired' => round($avgDaysExpired, 1)
                ],
                'generated_at' => Carbon::now()->format('d/m/Y H:i:s'),
                'generated_by' => auth()->user()->name ?? 'System'
            ];

            // Generate PDF
            $pdf = PDF::loadView('pdf.expired-items-report', $data);
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'laporan-barang-kadaluarsa-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format period for display
     */
    private function formatPeriod($startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        }
        return 'Semua Periode';
    }
}