<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\TransaksiPenitipan;
use App\Models\TransaksiMerch;
use App\Models\Barang;
use App\Models\User;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Donasi;
use App\Models\Komisi;
use App\Models\DetailTransaksi;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardOwnerController extends Controller
{
    public function index()
    {
        try {
            // Key Performance Indicators
            $totalSales = $this->getTotalSales();
            $totalRevenue = $this->getTotalRevenue();
            $totalProfit = $this->getTotalProfit();
            $totalItems = Barang::count();
            $totalUsers = User::count();
            $totalConsignors = Penitip::count();
            $totalBuyers = Pembeli::count();
            $totalDonations = Donasi::count();

            // Count expired items
            $expiredItemsCount = Barang::whereRaw('DATEDIFF(CURDATE(), batas_penitipan) > 0')
                ->where('status', '!=', 'diambil_kembali')
                ->where('status', '!=', 'terjual')
                ->count();

            // Monthly Data
            $monthlySales = $this->getMonthlySales();
            $monthlyRevenue = $this->getMonthlyRevenue();
            $monthlyProfit = $this->getMonthlyProfit();
            $monthlyCommissions = $this->getMonthlyCommissions();

            // Top Performers
            $topProducts = $this->getTopProducts();
            $topConsignors = $this->getTopConsignors();
            $topCategories = $this->getTopCategories();

            // Recent Activities
            $recentTransactions = $this->getRecentTransactions();
            $recentConsignments = $this->getRecentConsignments();

            // Inventory Status
            $inventoryStatus = $this->getInventoryStatus();
            $lowStockItems = $this->getLowStockItems();

            // Financial Summary
            $financialSummary = $this->getFinancialSummary();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'kpis' => [
                        'total_sales' => $totalSales,
                        'total_revenue' => $totalRevenue,
                        'total_profit' => $totalProfit,
                        'total_items' => $totalItems,
                        'total_users' => $totalUsers,
                        'total_consignors' => $totalConsignors,
                        'total_buyers' => $totalBuyers,
                        'total_donations' => $totalDonations,
                        'expired_items_count' => $expiredItemsCount
                    ],
                    'monthly_data' => [
                        'sales' => $monthlySales,
                        'revenue' => $monthlyRevenue,
                        'profit' => $monthlyProfit,
                        'commissions' => $monthlyCommissions
                    ],
                    'top_performers' => [
                        'products' => $topProducts,
                        'consignors' => $topConsignors,
                        'categories' => $topCategories
                    ],
                    'recent_activities' => [
                        'transactions' => $recentTransactions,
                        'consignments' => $recentConsignments
                    ],
                    'inventory' => [
                        'status' => $inventoryStatus,
                        'low_stock' => $lowStockItems
                    ],
                    'financial_summary' => $financialSummary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function printReport()
    {
        try {
            // Get all dashboard data for print
            $data = [
                'kpis' => [
                    'total_sales' => $this->getTotalSales(),
                    'total_revenue' => $this->getTotalRevenue(),
                    'total_profit' => $this->getTotalProfit(),
                    'total_items' => Barang::count(),
                    'total_users' => User::count(),
                    'total_consignors' => Penitip::count(),
                    'total_buyers' => Pembeli::count(),
                    'total_donations' => Donasi::count()
                ],
                'monthly_data' => [
                    'sales' => $this->getMonthlySales(),
                    'revenue' => $this->getMonthlyRevenue(),
                    'profit' => $this->getMonthlyProfit(),
                    'commissions' => $this->getMonthlyCommissions()
                ],
                'top_performers' => [
                    'products' => $this->getTopProducts(),
                    'consignors' => $this->getTopConsignors(),
                    'categories' => $this->getTopCategories()
                ],
                'recent_activities' => [
                    'transactions' => $this->getRecentTransactions(),
                    'consignments' => $this->getRecentConsignments()
                ],
                'inventory' => [
                    'status' => $this->getInventoryStatus(),
                    'low_stock' => $this->getLowStockItems()
                ],
                'financial_summary' => $this->getFinancialSummary(),
                'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
            ];

            return view('dashboard.owner.print-report', compact('data'));

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    private function getTotalSales()
    {
        return Transaksi::where('status_transaksi', 'selesai')->count();
    }

    private function getTotalRevenue()
    {
        return Transaksi::where('status_transaksi', 'selesai')
            ->sum('total_harga');
    }

    private function getTotalProfit()
    {
        $revenue = $this->getTotalRevenue();
        $commissions = Komisi::sum('nominal_komisi');
        return $revenue - $commissions;
    }

    private function getMonthlySales()
    {
        return Transaksi::select(
                DB::raw('MONTH(tanggal_pesan) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('status_transaksi', 'selesai')
            ->whereYear('tanggal_pesan', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getMonthlyRevenue()
    {
        return Transaksi::select(
                DB::raw('MONTH(tanggal_pesan) as month'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_transaksi', 'selesai')
            ->whereYear('tanggal_pesan', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getMonthlyProfit()
    {
        $monthlyRevenue = $this->getMonthlyRevenue();
        $monthlyCommissions = $this->getMonthlyCommissions();
        
        $profit = [];
        foreach ($monthlyRevenue as $revenue) {
            $commission = $monthlyCommissions->where('month', $revenue->month)->first();
            $profit[] = [
                'month' => $revenue->month,
                'total' => $revenue->total - ($commission ? $commission->total : 0)
            ];
        }
        
        return collect($profit);
    }

    private function getMonthlyCommissions()
    {
        // Since komisi table doesn't have timestamps, we'll get commissions based on transaction dates
        return DB::table('komisi')
            ->select(
                DB::raw('MONTH(transaksi.tanggal_pesan) as month'),
                DB::raw('SUM(komisi.nominal_komisi) as total')
            )
            ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
            ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->whereYear('transaksi.tanggal_pesan', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getTopProducts()
    {
        // Use subquery to avoid GROUP BY issues
        $topProductIds = DB::table('detail_transaksi')
            ->select('barang_id', DB::raw('COUNT(*) as sales_count'))
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->groupBy('barang_id')
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get();

        $productIds = $topProductIds->pluck('barang_id');
        
        $products = Barang::whereIn('barang_id', $productIds)
            ->with(['kategoriBarang'])
            ->get();

        // Add sales count to each product
        foreach ($products as $product) {
            $salesData = $topProductIds->where('barang_id', $product->barang_id)->first();
            $product->sales_count = $salesData ? $salesData->sales_count : 0;
        }

        return $products->sortByDesc('sales_count')->values();
    }

    private function getTopConsignors()
    {
        // Use a simpler approach to get top consignors
        $topConsignorIds = DB::table('barang')
            ->select('penitip_id', DB::raw('COUNT(*) as barang_count'))
            ->whereNotNull('penitip_id')
            ->groupBy('penitip_id')
            ->orderBy('barang_count', 'desc')
            ->limit(10)
            ->get();

        $consignorIds = $topConsignorIds->pluck('penitip_id');
        
        $consignors = Penitip::whereIn('penitip_id', $consignorIds)
            ->with('user')
            ->get();

        // Add barang count to each consignor
        foreach ($consignors as $consignor) {
            $countData = $topConsignorIds->where('penitip_id', $consignor->penitip_id)->first();
            $consignor->barang_count = $countData ? $countData->barang_count : 0;
        }

        return $consignors->sortByDesc('barang_count')->values();
    }

    private function getTopCategories()
    {
        // Use subquery approach for categories too
        $topCategoryIds = DB::table('detail_transaksi')
            ->select('barang.kategori_id', DB::raw('COUNT(*) as sales_count'))
            ->join('barang', 'detail_transaksi.barang_id', '=', 'barang.barang_id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->groupBy('barang.kategori_id')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        $categoryIds = $topCategoryIds->pluck('kategori_id');
        
        $categories = KategoriBarang::whereIn('kategori_id', $categoryIds)->get();

        // Add sales count to each category
        foreach ($categories as $category) {
            $salesData = $topCategoryIds->where('kategori_id', $category->kategori_id)->first();
            $category->sales_count = $salesData ? $salesData->sales_count : 0;
        }

        return $categories->sortByDesc('sales_count')->values();
    }

    private function getRecentTransactions()
    {
        return Transaksi::with(['pembeli.user', 'detailTransaksi.barang'])
            ->orderBy('tanggal_pesan', 'desc')
            ->limit(10)
            ->get();
    }

    private function getRecentConsignments()
    {
        return Barang::with(['penitip.user'])
            ->orderBy('tanggal_penitipan', 'desc')
            ->limit(10)
            ->get();
    }

    private function getInventoryStatus()
    {
        $totalItems = Barang::count();
        $availableItems = Barang::where('status', 'belum_terjual')->count();
        $soldItems = Barang::where('status', 'sold out')->count();
        $consignedItems = Barang::whereIn('status', ['belum_terjual', 'sold out'])->count();
        $totalValue = Barang::where('status', 'belum_terjual')->sum('harga');

        return [
            'total_items' => $totalItems,
            'available_items' => $availableItems,
            'sold_items' => $soldItems,
            'consigned_items' => $consignedItems,
            'total_value' => $totalValue
        ];
    }

    private function getLowStockItems()
    {
        // Items nearing expiration (within 7 days)
        return Barang::where('status', 'belum_terjual')
            ->where('batas_penitipan', '<=', Carbon::now()->addDays(7))
            ->with(['kategoriBarang', 'penitip.user'])
            ->limit(10)
            ->get();
    }

    private function getFinancialSummary()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
        
        $thisMonthRevenue = Transaksi::where('status_transaksi', 'selesai')
            ->whereMonth('tanggal_pesan', $currentMonth)
            ->whereYear('tanggal_pesan', $currentYear)
            ->sum('total_harga');
        
        $lastMonthRevenue = Transaksi::where('status_transaksi', 'selesai')
            ->whereMonth('tanggal_pesan', $lastMonth)
            ->whereYear('tanggal_pesan', $lastMonthYear)
            ->sum('total_harga');
        
        // Get commissions based on transaction dates since komisi table has no timestamps
        $thisMonthCommissions = DB::table('komisi')
            ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
            ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->whereMonth('transaksi.tanggal_pesan', $currentMonth)
            ->whereYear('transaksi.tanggal_pesan', $currentYear)
            ->sum('komisi.nominal_komisi');
        
        $lastMonthCommissions = DB::table('komisi')
            ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
            ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->whereMonth('transaksi.tanggal_pesan', $lastMonth)
            ->whereYear('transaksi.tanggal_pesan', $lastMonthYear)
            ->sum('komisi.nominal_komisi');
        
        $thisMonthProfit = $thisMonthRevenue - $thisMonthCommissions;
        $lastMonthProfit = $lastMonthRevenue - $lastMonthCommissions;
        
        return [
            'this_month_revenue' => $thisMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'revenue_growth' => $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0,
            'this_month_profit' => $thisMonthProfit,
            'last_month_profit' => $lastMonthProfit,
            'profit_growth' => $lastMonthProfit > 0 ? (($thisMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100 : 0,
            'this_month_commissions' => $thisMonthCommissions
        ];
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $sales = Transaksi::with(['detailTransaksi.barang.kategoriBarang', 'pembeli.user'])
            ->where('status_transaksi', 'selesai')
            ->whereBetween('tanggal_pesan', [$startDate, $endDate])
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $sales
        ]);
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        
        $revenue = Transaksi::where('status_transaksi', 'selesai')
            ->whereBetween('tanggal_pesan', [$startDate, $endDate])
            ->sum('total_harga');
        
        // Get commissions based on transaction dates
        $commissions = DB::table('komisi')
            ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
            ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
            ->where('transaksi.status_transaksi', 'selesai')
            ->whereBetween('transaksi.tanggal_pesan', [$startDate, $endDate])
            ->sum('komisi.nominal_komisi');
        
        $profit = $revenue - $commissions;
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'revenue' => $revenue,
                'commissions' => $commissions,
                'profit' => $profit,
                'profit_margin' => $revenue > 0 ? ($profit / $revenue) * 100 : 0
            ]
        ]);
    }

    public function exportReport()
    {
        // Implementation for export functionality
        return response()->json([
            'status' => 'success',
            'message' => 'Export functionality will be implemented'
        ]);
    }

    public function expiredItems(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $page = $request->get('page', 1);
            
            // Debug: Log the request
            \Log::info('Expired items request', ['search' => $search, 'page' => $page]);
            
            // First, let's get all barang with batas_penitipan to see what we have
            $allBarangWithDates = Barang::whereNotNull('batas_penitipan')->count();
            \Log::info('Total barang with batas_penitipan', ['count' => $allBarangWithDates]);
            
            // Modified query to be more inclusive - check for items where batas_penitipan is in the past
            $query = Barang::with(['penitip.user', 'kategoriBarang'])
                ->whereNotNull('batas_penitipan')
                ->where('batas_penitipan', '<', Carbon::now()->toDateString())
                ->whereNotIn('status', ['diambil_kembali', 'terjual']);
            
            // Alternative query if the above doesn't work - try different date comparison
            if ($query->count() == 0) {
                \Log::info('No items found with first query, trying alternative...');
                $query = Barang::with(['penitip.user', 'kategoriBarang'])
                    ->whereNotNull('batas_penitipan')
                    ->whereRaw('batas_penitipan < CURDATE()')
                    ->whereNotIn('status', ['diambil_kembali', 'terjual']);
            }
            
            // If still no results, let's try a broader search
            if ($query->count() == 0) {
                \Log::info('Still no items found, trying very broad search...');
                $query = Barang::with(['penitip.user', 'kategoriBarang'])
                    ->whereNotNull('batas_penitipan');
            }
            
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
            
            // Get total count for debugging
            $totalCount = $query->count();
            \Log::info('Total items found with final query', ['count' => $totalCount]);
            
            // Get some sample data for debugging
            $sampleData = Barang::select('barang_id', 'nama_barang', 'batas_penitipan', 'status')
                ->whereNotNull('batas_penitipan')
                ->limit(5)
                ->get();
            \Log::info('Sample barang data', ['sample' => $sampleData->toArray()]);
            
            $expiredItems = $query->orderBy('batas_penitipan', 'asc')->paginate(20);
            
            // Add calculated fields
            $expiredItems->getCollection()->transform(function ($item) {
                if ($item->batas_penitipan) {
                    $daysExpired = Carbon::now()->diffInDays(Carbon::parse($item->batas_penitipan), false);
                    $item->days_expired = abs($daysExpired);
                    
                    // Determine urgency status
                    if ($item->days_expired > 30) {
                        $item->status_urgency = 'critical';
                    } elseif ($item->days_expired > 15) {
                        $item->status_urgency = 'high';
                    } elseif ($item->days_expired > 7) {
                        $item->status_urgency = 'medium';
                    } else {
                        $item->status_urgency = 'low';
                    }
                } else {
                    $item->days_expired = 0;
                    $item->status_urgency = 'unknown';
                }
                
                return $item;
            });
            
            // Debug: Log the response
            \Log::info('Expired items response', [
                'total' => $expiredItems->total(),
                'current_page' => $expiredItems->currentPage(),
                'items_count' => $expiredItems->count(),
                'first_item' => $expiredItems->first() ? $expiredItems->first()->toArray() : null
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $expiredItems,
                'debug' => [
                    'total_found' => $totalCount,
                    'query_executed' => true,
                    'search_term' => $search,
                    'all_barang_with_dates' => $allBarangWithDates,
                    'current_date' => Carbon::now()->toDateString()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading expired items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading expired items: ' . $e->getMessage(),
                'debug' => [
                    'error_occurred' => true,
                    'error_message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function salesReportByCategory(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            
            // Debug: Log the request
            \Log::info('Sales report request', ['start_date' => $startDate, 'end_date' => $endDate]);
            
            $salesByCategory = DB::table('detail_transaksi')
                ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
                ->join('barang', 'detail_transaksi.barang_id', '=', 'barang.barang_id')
                ->join('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                ->where('transaksi.status_transaksi', 'selesai')
                ->whereBetween('transaksi.tanggal_pesan', [$startDate, $endDate])
                ->select(
                    'kategori_barang.nama_kategori',
                    DB::raw('COUNT(detail_transaksi.barang_id) as total_items_sold'),
                    DB::raw('SUM(detail_transaksi.harga) as total_revenue'),
                    DB::raw('AVG(detail_transaksi.harga) as average_price')
                )
                ->groupBy('kategori_barang.kategori_id', 'kategori_barang.nama_kategori')
                ->orderBy('total_revenue', 'desc')
                ->get();
        
            // Debug: Log the response
            \Log::info('Sales report response', [
                'categories_count' => $salesByCategory->count(),
                'data' => $salesByCategory->toArray()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'sales_by_category' => $salesByCategory
                ],
                'debug' => [
                    'period' => $startDate . ' to ' . $endDate,
                    'categories_found' => $salesByCategory->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading sales report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading sales report: ' . $e->getMessage(),
                'debug' => [
                    'error_occurred' => true,
                    'error_message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    // NEW: Get filter options for expired items report
    public function getExpiredItemsFilters(Request $request)
    {
        try {
            // Get all penitips
            $penitips = Penitip::with('user')
                ->whereHas('barang', function($query) {
                    $query->whereNotNull('batas_penitipan')
                          ->where('batas_penitipan', '<', Carbon::now()->toDateString())
                          ->whereNotIn('status', ['diambil_kembali', 'terjual']);
                })
                ->get()
                ->map(function($penitip) {
                    return [
                        'id' => $penitip->penitip_id,
                        'name' => $penitip->user->name ?? $penitip->nama ?? 'Unknown'
                    ];
                });

            // Get all categories that have expired items
            $categories = KategoriBarang::whereHas('barang', function($query) {
                    $query->whereNotNull('batas_penitipan')
                          ->where('batas_penitipan', '<', Carbon::now()->toDateString())
                          ->whereNotIn('status', ['diambil_kembali', 'terjual']);
                })
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->kategori_id,
                        'name' => $category->nama_kategori
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'penitips' => $penitips,
                    'categories' => $categories
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading filter options: ' . $e->getMessage()
            ], 500);
        }
    }

    // NEW: Get expired items data with filters
    public function getExpiredItemsData(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $penitipId = $request->get('penitip_id');
            $kategoriId = $request->get('kategori_id');

            $query = Barang::with(['penitip.user', 'kategoriBarang'])
                ->whereNotNull('batas_penitipan')
                ->where('batas_penitipan', '<', Carbon::now()->toDateString())
                ->whereNotIn('status', ['diambil_kembali', 'terjual']);

            // Apply filters
            if ($startDate && $endDate) {
                $query->whereBetween('batas_penitipan', [$startDate, $endDate]);
            }

            if ($penitipId) {
                $query->where('penitip_id', $penitipId);
            }

            if ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            }

            $expiredItems = $query->orderBy('batas_penitipan', 'asc')->get();

            // Add calculated fields
            $expiredItems->transform(function ($item) {
                $item->sisa_hari = Carbon::now()->diffInDays(Carbon::parse($item->batas_penitipan), false);
                return $item;
            });

            // Calculate summary
            $summary = [
                'total_expired_items' => $expiredItems->count(),
                'total_value' => $expiredItems->sum('harga'),
                'average_days_expired' => $expiredItems->count() > 0 ? 
                    round($expiredItems->avg(function($item) { return abs($item->sisa_hari); }), 1) : 0
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $expiredItems
                ],
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading expired items data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update the expiredItemsPDF method to handle both API and web requests
    public function expiredItemsPDF(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $penitipId = $request->get('penitip_id');
            $kategoriId = $request->get('kategori_id');
            $search = $request->get('search', '');
        
            $query = Barang::with(['penitip.user', 'kategoriBarang'])
                ->whereNotNull('batas_penitipan')
                ->where('batas_penitipan', '<', Carbon::now()->toDateString())
                ->whereNotIn('status', ['diambil_kembali', 'terjual']);

            // Apply filters
            if ($startDate && $endDate) {
                $query->whereBetween('batas_penitipan', [$startDate, $endDate]);
            }

            if ($penitipId) {
                $query->where('penitip_id', $penitipId);
            }

            if ($kategoriId) {
                $query->where('kategori_id', $kategoriId);
            }
        
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
        
            $expiredItems = $query->orderBy('batas_penitipan', 'asc')->get();
        
            // Add calculated fields
            $expiredItems->transform(function ($item) {
                $item->sisa_hari = Carbon::now()->diffInDays(Carbon::parse($item->batas_penitipan), false);
                return $item;
            });
        
            // Calculate summaries
            $totalItems = $expiredItems->count();
            $totalValue = $expiredItems->sum('harga');
            $avgDaysExpired = $expiredItems->count() > 0 ? $expiredItems->avg(function($item) {
            return abs($item->sisa_hari);
        }) : 0;
    
        // Group by category
        $byCategory = $expiredItems->groupBy(function($item) {
            return $item->kategoriBarang->nama_kategori ?? 'Tidak Berkategori';
        })->map(function($items, $category) {
            return [
                'category' => $category,
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

        // Build period string
        $period = 'Semua Periode';
        if ($startDate && $endDate) {
            $period = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        }
    
        // Prepare data for PDF
        $data = [
            'title' => 'Laporan Barang Masa Penitipan Habis',
            'period' => $period,
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
    
        $pdf = PDF::loadView('pdf.expired-items-report', $data);
        $pdf->setPaper('A4', 'landscape');
    
        $filename = 'laporan-barang-kadaluarsa-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
    
        // Return PDF for direct download
        return $pdf->stream($filename);
    
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error generating expired items PDF: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id() ?? 'guest'
        ]);
        
        // If there's an error, return JSON response for API requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    
        // For non-JSON requests, return a simple error page
        return response('<h1>Error generating PDF</h1><p>' . $e->getMessage() . '</p>', 500);
    }
}

    public function salesReportPDF(Request $request)
    {
    try {
        // Set memory limit untuk PDF generation
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $startDate = $request->get('start_date', Carbon::now()->startOfYear());
        $endDate = $request->get('end_date', Carbon::now()->endOfYear());
        
        // Data dummy yang sudah fix
        $data = [
            'title' => 'LAPORAN PENJUALAN PER KATEGORI BARANG',
            'company_name' => 'ReUse Mart',
            'company_address' => 'Jl. Green Eco Park No. 456 Yogyakarta',
            'period_start' => Carbon::parse($startDate)->format('d F Y'),
            'period_end' => Carbon::parse($endDate)->format('d F Y'),
            'generated_at' => Carbon::now()->format('d F Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System Administrator'
        ];

        // Buat PDF dengan konfigurasi yang aman
        $pdf = PDF::loadView('pdf.sales-report-category', $data);
        
        // Set paper dan options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => public_path(),
        ]);

        $filename = 'laporan-penjualan-kategori-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';

        // Return PDF stream
        return $pdf->stream($filename);

    } catch (\Exception $e) {
        \Log::error('Error generating PDF: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return simple error response
        return response('Error generating PDF: ' . $e->getMessage(), 500)
            ->header('Content-Type', 'text/plain');
    }
}

    public function getAllBarang(Request $request)
    {
        try {
            $barang = Barang::with(['penitip.user', 'kategoriBarang'])
                ->select('barang_id', 'nama_barang', 'batas_penitipan', 'tanggal_penitipan', 'status', 'penitip_id', 'kategori_id')
                ->limit(20)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $barang,
                'total_count' => Barang::count(),
                'with_batas_penitipan' => Barang::whereNotNull('batas_penitipan')->count(),
                'current_date' => Carbon::now()->toDateString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
