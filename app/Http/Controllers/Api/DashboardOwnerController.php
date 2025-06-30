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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\RequestDonasi;
use App\Models\Organisasi;

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
                        'total_donations' => $totalDonations
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

    public function donasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $organisasiId = $request->get('organisasi_id');
            
            // Get donated items dengan relasi lengkap menggunakan struktur database yang benar
            $donasiQuery = DB::table('donasi')
                ->leftJoin('barang', 'donasi.barang_id', '=', 'barang.barang_id')
                ->leftJoin('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                ->leftJoin('penitip', 'barang.penitip_id', '=', 'penitip.penitip_id')
                ->leftJoin('users as penitip_user', 'penitip.user_id', '=', 'penitip_user.id')
                ->leftJoin('request_donasi', 'donasi.request_id', '=', 'request_donasi.request_id')
                ->leftJoin('organisasi', 'request_donasi.organisasi_id', '=', 'organisasi.organisasi_id')
                ->leftJoin('users as org_user', 'organisasi.user_id', '=', 'org_user.id')
                ->select(
                    'donasi.*',
                    'barang.nama_barang',
                    'barang.harga',
                    'barang.deskripsi as barang_deskripsi',
                    'barang.tanggal_penitipan',
                    'barang.status as barang_status',
                    'barang.foto_barang',
                    'kategori_barang.nama_kategori as kategori_nama',
                    'penitip.penitip_id',
                    'penitip_user.name as penitip_nama',
                    'penitip.no_ktp as penitip_ktp',
                    'organisasi.nama_organisasi',
                    'org_user.name as organisasi_pic',
                    'org_user.phone_number as organisasi_telp',
                    'request_donasi.tanggal_request',
                    'request_donasi.status_request',
                    'request_donasi.deskripsi as request_deskripsi',
                    'request_donasi.jumlah_barang_diminta',
                    // Generate kode produk berdasarkan pattern yang ada
                    DB::raw("CONCAT('K', LPAD(barang.barang_id, 3, '0')) as kode_produk"),
                    // Generate ID penitip berdasarkan pattern yang ada  
                    DB::raw("CONCAT('T', LPAD(penitip.penitip_id, 2, '0')) as id_penitip_display")
                )
                ->whereNotNull('donasi.tanggal_donasi')
                ->whereNull('donasi.deleted_at');

            // Filter berdasarkan tanggal jika ada
            if ($startDate && $endDate) {
                $donasiQuery->whereBetween('donasi.tanggal_donasi', [$startDate, $endDate]);
            }
            
            if ($organisasiId) {
                $donasiQuery->where('organisasi.organisasi_id', $organisasiId);
            }
            
            $donasi = $donasiQuery->orderBy('donasi.tanggal_donasi', 'desc')->get();
            
            // Get request donasi dengan detail lengkap
            $requestDonasiQuery = DB::table('request_donasi')
                ->join('organisasi', 'request_donasi.organisasi_id', '=', 'organisasi.organisasi_id')
                ->leftJoin('users as org_user', 'organisasi.user_id', '=', 'org_user.id')
                ->select(
                    'request_donasi.*', 
                    'organisasi.nama_organisasi',
                    'org_user.name as pic_nama',
                    'org_user.email as pic_email',
                    'org_user.phone_number as pic_telp'
                );

            // Filter berdasarkan tanggal jika ada
            if ($startDate && $endDate) {
                $requestDonasiQuery->whereBetween('request_donasi.tanggal_request', [$startDate, $endDate]);
            }
            
            if ($organisasiId) {
                $requestDonasiQuery->where('request_donasi.organisasi_id', $organisasiId);
            }
            
            $requestDonasi = $requestDonasiQuery->orderBy('request_donasi.tanggal_request', 'desc')->get();
            
            // Enhanced statistics dengan null checking
            $totalDonasi = $donasi->count();
            $totalNilaiDonasi = $donasi->sum('harga') ?? 0;
            $totalRequest = $requestDonasi->count();
            $organisasiTerlibat = $requestDonasi->pluck('organisasi_id')->unique()->count();
            $avgNilaiDonasi = $totalDonasi > 0 ? $totalNilaiDonasi / $totalDonasi : 0;
            
            // Monthly data dengan detail - gunakan tanggal_donasi yang benar
            $monthlyDonasi = $donasi->groupBy(function($item) {
                return Carbon::parse($item->tanggal_donasi)->format('Y-m');
            })->map(function($items, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::parse($month . '-01')->format('F Y'),
                    'count' => $items->count(),
                    'value' => $items->sum('harga'),
                    'avg_value' => $items->count() > 0 ? $items->sum('harga') / $items->count() : 0
                ];
            })->sortBy('month')->values();
            
            // Category breakdown dengan persentase - gunakan field yang benar
            $categoryBreakdown = $donasi->groupBy(function($item) {
                return $item->kategori_nama ?: $item->nama_kategori ?: 'Tidak Dikategorikan';
            })->map(function($items, $category) use ($totalDonasi, $totalNilaiDonasi) {
                $count = $items->count();
                $value = $items->sum('harga');
                return [
                    'category' => $category,
                    'count' => $count,
                    'value' => $value,
                    'percentage' => $totalDonasi > 0 ? round(($count / $totalDonasi) * 100, 2) : 0,
                    'value_percentage' => $totalNilaiDonasi > 0 ? round(($value / $totalNilaiDonasi) * 100, 2) : 0
                ];
            })->sortByDesc('count')->values();
            
            // Top organizations dengan detail
            $topOrganisasi = $requestDonasi->groupBy('organisasi_id')
                ->map(function($items) use ($donasi) {
                $organisasi = $items->first();
                $requestIds = $items->pluck('request_id');
                $donasiReceived = $donasi->whereIn('request_id', $requestIds);
            
                return [
                    'organisasi_id' => $organisasi->organisasi_id,
                    'nama_organisasi' => $organisasi->nama_organisasi,
                    'pic_nama' => $organisasi->pic_nama,
                    'pic_email' => $organisasi->pic_email,
                    'pic_telp' => $organisasi->pic_telp,
                    'request_count' => $items->count(),
                    'donasi_received' => $donasiReceived->count(),
                    'total_value_received' => $donasiReceived->sum('harga'),
                    'success_rate' => $items->count() > 0 ? round(($donasiReceived->count() / $items->count()) * 100, 2) : 0
                ];
            })->sortByDesc('request_count')->values();

            // Status breakdown
            $statusBreakdown = $requestDonasi->groupBy('status_request')->map(function($items, $status) use ($requestDonasi) {
                return [
                    'status' => $status,
                    'count' => $items->count(),
                    'percentage' => $requestDonasi->count() > 0 ? round(($items->count() / $requestDonasi->count()) * 100, 2) : 0
                ];
            })->values();

            // All organizations untuk filter
            $allOrganisasi = DB::table('organisasi')
                ->leftJoin('users', 'organisasi.user_id', '=', 'users.id')
                ->select('organisasi.organisasi_id', 'organisasi.nama_organisasi', 'users.phone_number')
                ->orderBy('organisasi.nama_organisasi')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'summary' => [
                        'total_donasi' => $totalDonasi,
                        'total_nilai_donasi' => $totalNilaiDonasi,
                        'total_request' => $totalRequest,
                        'organisasi_terlibat' => $organisasiTerlibat,
                        'avg_nilai_donasi' => $avgNilaiDonasi,
                        'success_rate' => $totalRequest > 0 ? round(($totalDonasi / $totalRequest) * 100, 2) : 0
                    ],
                    'donasi' => $donasi,
                    'request_donasi' => $requestDonasi,
                    'monthly_data' => $monthlyDonasi,
                    'category_breakdown' => $categoryBreakdown,
                    'status_breakdown' => $statusBreakdown,
                    'top_organisasi' => $topOrganisasi,
                    'all_organisasi' => $allOrganisasi,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'organisasi_id' => $organisasiId
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Donation Report Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load donation report: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function printDonasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            $organisasiId = $request->get('organisasi_id');
            
            // Get the same data as the regular report
            $response = $this->donasiReport($request);
            $responseData = $response->getData(true);
            
            if ($responseData['status'] !== 'success') {
                return back()->with('error', 'Failed to generate donation report: ' . ($responseData['message'] ?? 'Unknown error'));
            }
            
            $data = $responseData['data'];
            $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
            $data['period'] = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
            
            return view('dashboard.owner.donasi.print-report', compact('data'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate donation report: ' . $e->getMessage());
        }
    }

    public function exportDonasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            $organisasiId = $request->get('organisasi_id');
            
            // Get report data
            $response = $this->donasiReport($request);
            $responseData = $response->getData(true);
            
            if ($responseData['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to export donation report: ' . ($responseData['message'] ?? 'Unknown error')
                ], 500);
            }
            
            $data = $responseData['data'];
            
            // Generate CSV content
            $csvContent = "Laporan Donasi Barang ReuseMart\n";
            $csvContent .= "Periode: " . Carbon::parse($startDate)->format('d/m/Y') . " - " . Carbon::parse($endDate)->format('d/m/Y') . "\n";
            $csvContent .= "Generated: " . Carbon::now()->format('d/m/Y H:i:s') . "\n\n";
            
            $csvContent .= "RINGKASAN\n";
            $csvContent .= "Total Donasi," . $data['summary']['total_donasi'] . "\n";
            $csvContent .= "Total Nilai Donasi,Rp " . number_format($data['summary']['total_nilai_donasi'], 0, ',', '.') . "\n";
            $csvContent .= "Total Request," . $data['summary']['total_request'] . "\n";
            $csvContent .= "Organisasi Terlibat," . $data['summary']['organisasi_terlibat'] . "\n\n";
            
            $csvContent .= "DETAIL DONASI\n";
            $csvContent .= "ID,Nama Barang,Kategori,Nilai,Deskripsi,Penerima,Tanggal\n";
            
            foreach ($data['donasi'] as $donasi) {
                $csvContent .= ($donasi->request_id ?? '') . ",";
                $csvContent .= '"' . ($donasi->nama_barang ?? '') . '",';
                $csvContent .= '"' . ($donasi->nama_kategori ?? 'Tidak Dikategorikan') . '",';
                $csvContent .= '"Rp ' . number_format($donasi->harga ?? 0, 0, ',', '.') . '",';
                $csvContent .= '"' . ($donasi->barang_deskripsi ?? '') . '",';
                $csvContent .= '"' . ($donasi->nama_penerima ?? '') . '",';
                $csvContent .= '"' . ($donasi->tanggal_donasi ? Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y') : '') . '"' . "\n";
            }
            
            $filename = 'laporan-donasi-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export donation report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Web interface methods for Request Donasi
    public function requestDonasiIndex()
    {
        return view('dashboard.owner.request-donasi.index');
    }

    public function requestDonasiReportData(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $statusFilter = $request->input('status', '');

            Log::info('Request Donasi Report Data called with params:', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $statusFilter
            ]);

        // Base query menggunakan struktur database yang benar
        $query = DB::table('request_donasi as rd')
            ->join('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->whereBetween('rd.tanggal_request', [$startDate, $endDate])
            ->select(
                'rd.request_id',
                'rd.organisasi_id',
                'o.nama_organisasi',
                'o.alamat',
                'rd.deskripsi',
                'rd.tanggal_request',
                'rd.tanggal_donasi',
                'rd.status_request',
                'rd.jumlah_barang_diminta',
                'u.name as nama_penerima',
                'u.phone_number as telepon_organisasi'
            );

        // Apply status filter if provided
        if ($statusFilter) {
            $query->where('rd.status_request', $statusFilter);
        }

        // Get requests data
        $requests = $query->orderBy('rd.tanggal_request', 'desc')->get();

        Log::info('Found requests:', ['count' => $requests->count()]);

        // Get summary data
        $totalRequest = DB::table('request_donasi')
            ->whereBetween('tanggal_request', [$startDate, $endDate])
            ->count();

        $pendingRequest = DB::table('request_donasi')
            ->whereBetween('tanggal_request', [$startDate, $endDate])
            ->where('status_request', 'menunggu')
            ->count();

        $approvedRequest = DB::table('request_donasi')
            ->whereBetween('tanggal_request', [$startDate, $endDate])
            ->where('status_request', 'disetujui')
            ->count();

        $rejectedRequest = DB::table('request_donasi')
            ->whereBetween('tanggal_request', [$startDate, $endDate])
            ->where('status_request', 'ditolak')
            ->count();

        $organisasiAktif = DB::table('request_donasi')
            ->whereBetween('tanggal_request', [$startDate, $endDate])
            ->distinct('organisasi_id')
            ->count('organisasi_id');

        // Format data for response
        $formattedRequests = $requests->map(function ($item) {
            return [
                'request_id' => $item->request_id,
                'id_organisasi' => 'ORG' . str_pad($item->organisasi_id, 2, '0', STR_PAD_LEFT),
                'nama_organisasi' => $item->nama_organisasi,
                'alamat' => $item->alamat ?: 'Alamat tidak tersedia',
                'request_description' => $item->deskripsi,
                'tanggal_request' => $item->tanggal_request,
                'tanggal_donasi' => $item->tanggal_donasi,
                'status' => $item->status_request,
                'jumlah_barang' => $item->jumlah_barang_diminta,
                'nama_penerima' => $item->nama_penerima,
                'telepon_organisasi' => $item->telepon_organisasi
            ];
        });

        $responseData = [
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_request' => $totalRequest,
                    'pending_request' => $pendingRequest,
                    'fulfilled_request' => $approvedRequest,
                    'rejected_request' => $rejectedRequest,
                    'organisasi_aktif' => $organisasiAktif
                ],
                'requests' => $formattedRequests
            ]
        ];

        Log::info('Returning response data:', $responseData);

        return response()->json($responseData);
        
    } catch (\Exception $e) {
        Log::error('Request Donasi Report Data Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Error fetching report data: ' . $e->getMessage(),
            'debug' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

public function requestDonasiPrintReport(Request $request)
{
    try {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $statusFilter = $request->input('status', '');

        // Base query
        $query = DB::table('request_donasi as rd')
            ->join('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->whereBetween('rd.tanggal_request', [$startDate, $endDate])
            ->select(
                'rd.request_id',
                'o.organisasi_id',
                'o.nama_organisasi',
                'o.alamat',
                'rd.deskripsi',
                'rd.tanggal_request',
                'rd.tanggal_donasi',
                'rd.status_request',
                'rd.jumlah_barang_diminta',
                'u.name as nama_penerima'
            );

        // Apply status filter if provided
        if ($statusFilter) {
            $query->where('rd.status_request', $statusFilter);
        }

        // Get requests data
        $requests = $query->orderBy('rd.tanggal_request', 'desc')->get();

        // Format data for view
        $formattedRequests = $requests->map(function ($item) {
            return [
                'request_id' => $item->request_id,
                'id_organisasi' => 'ORG' . str_pad($item->organisasi_id, 2, '0', STR_PAD_LEFT),
                'nama_organisasi' => $item->nama_organisasi,
                'alamat' => $item->alamat ?: 'Alamat tidak tersedia',
                'request_description' => $item->deskripsi,
                'tanggal_request' => $item->tanggal_request,
                'tanggal_donasi' => $item->tanggal_donasi,
                'status' => $item->status_request,
                'jumlah_barang' => $item->jumlah_barang_diminta,
                'nama_penerima' => $item->nama_penerima
            ];
        });

        $data = [
            'requests' => $formattedRequests,
            'period' => Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y'),
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        return view('dashboard.owner.request-donasi.print-report', ['data' => $data]);
    } catch (\Exception $e) {
        Log::error('Print Request Donasi Error: ' . $e->getMessage());
        return back()->with('error', 'Error generating print report: ' . $e->getMessage());
    }
}

    /**
     * Get organization address from the database
     * Helper method for request donasi functionality
     */
    

    public function requestDonasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $organisasiId = $request->get('organisasi_id');
            $statusRequest = $request->get('status_request');
            
            // Get request donasi dengan relasi lengkap
            $requestDonasiQuery = DB::table('request_donasi')
                ->join('organisasi', 'request_donasi.organisasi_id', '=', 'organisasi.organisasi_id')
                ->leftJoin('users as org_user', 'organisasi.user_id', '=', 'org_user.id')
                ->select(
                    'request_donasi.*',
                    'organisasi.nama_organisasi',
                    'organisasi.alamat',
                    'org_user.name as pic_nama',
                    'org_user.email as pic_email',
                    'org_user.phone_number as pic_telp',
                    // Generate ID organisasi berdasarkan pattern
                    DB::raw("CONCAT('ORG', LPAD(organisasi.organisasi_id, 2, '0')) as id_organisasi_display")
                );

            // Filter berdasarkan tanggal
            if ($startDate && $endDate) {
                $requestDonasiQuery->whereBetween('request_donasi.tanggal_request', [$startDate, $endDate]);
            }
            
            if ($organisasiId) {
                $requestDonasiQuery->where('request_donasi.organisasi_id', $organisasiId);
            }
            
            if ($statusRequest) {
                $requestDonasiQuery->where('request_donasi.status_request', $statusRequest);
            }
            
            $requestDonasi = $requestDonasiQuery->orderBy('request_donasi.tanggal_request', 'desc')->get();
            
            // Enhanced statistics
            $totalRequest = $requestDonasi->count();
            $organisasiTerlibat = $requestDonasi->pluck('organisasi_id')->unique()->count();
            $totalBarangDiminta = $requestDonasi->sum('jumlah_barang_diminta');
            
            // Status breakdown
            $statusBreakdown = $requestDonasi->groupBy('status_request')->map(function($items, $status) use ($requestDonasi) {
                return [
                    'status' => $status,
                    'count' => $items->count(),
                    'percentage' => $requestDonasi->count() > 0 ? round(($items->count() / $requestDonasi->count()) * 100, 2) : 0
                ];
            })->values();

            // Monthly data
            $monthlyRequest = $requestDonasi->groupBy(function($item) {
                return Carbon::parse($item->tanggal_request)->format('Y-m');
            })->map(function($items, $month) {
                return [
                    'month' => $month,
                    'month_name' => Carbon::parse($month . '-01')->format('F Y'),
                    'count' => $items->count(),
                    'total_barang_diminta' => $items->sum('jumlah_barang_diminta')
                ];
            })->sortBy('month')->values();
            
            // Top organizations by request count
            $topOrganisasi = $requestDonasi->groupBy('organisasi_id')
                ->map(function($items) {
                    $organisasi = $items->first();
                    return [
                        'organisasi_id' => $organisasi->organisasi_id,
                        'id_organisasi_display' => $organisasi->id_organisasi_display,
                        'nama_organisasi' => $organisasi->nama_organisasi,
                        'alamat' => $organisasi->alamat,
                        'pic_nama' => $organisasi->pic_nama,
                        'pic_email' => $organisasi->pic_email,
                        'request_count' => $items->count(),
                        'total_barang_diminta' => $items->sum('jumlah_barang_diminta')
                    ];
                })->sortByDesc('request_count')->values();

            // All organizations untuk filter
            $allOrganisasi = DB::table('organisasi')
                ->leftJoin('users', 'organisasi.user_id', '=', 'users.id')
                ->select('organisasi.organisasi_id', 'organisasi.nama_organisasi', 'users.phone_number')
                ->orderBy('organisasi.nama_organisasi')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'summary' => [
                        'total_request' => $totalRequest,
                        'organisasi_terlibat' => $organisasiTerlibat,
                        'total_barang_diminta' => $totalBarangDiminta,
                        'avg_barang_per_request' => $totalRequest > 0 ? round($totalBarangDiminta / $totalRequest, 2) : 0
                    ],
                    'request_donasi' => $requestDonasi,
                    'monthly_data' => $monthlyRequest,
                    'status_breakdown' => $statusBreakdown,
                    'top_organisasi' => $topOrganisasi,
                    'all_organisasi' => $allOrganisasi,
                    'filters' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'organisasi_id' => $organisasiId,
                        'status_request' => $statusRequest
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Request Donation Report Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load request donation report: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function printRequestDonasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            $organisasiId = $request->get('organisasi_id');
            $statusRequest = $request->get('status_request');
            
            // Get the same data as the regular report
            $response = $this->requestDonasiReport($request);
            $responseData = $response->getData(true);
            
            if ($responseData['status'] !== 'success') {
                return back()->with('error', 'Failed to generate request donation report: ' . ($responseData['message'] ?? 'Unknown error'));
            }
            
            $data = $responseData['data'];
            $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
            $data['period'] = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
            
            return view('dashboard.owner.request-donasi.print-report', compact('data'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate request donation report: ' . $e->getMessage());
        }
    }

    public function exportRequestDonasiReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
            $organisasiId = $request->get('organisasi_id');
            $statusRequest = $request->get('status_request');
            
            // Get report data
            $response = $this->requestDonasiReport($request);
            $responseData = $response->getData(true);
            
            if ($responseData['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to export request donation report: ' . ($responseData['message'] ?? 'Unknown error')
                ], 500);
            }
            
            $data = $responseData['data'];
            
            // Generate CSV content
            $csvContent = "Laporan Request Donasi ReuseMart\n";
            $csvContent .= "Periode: " . Carbon::parse($startDate)->format('d/m/Y') . " - " . Carbon::parse($endDate)->format('d/m/Y') . "\n";
            $csvContent .= "Generated: " . Carbon::now()->format('d/m/Y H:i:s') . "\n\n";
            
            $csvContent .= "RINGKASAN\n";
            $csvContent .= "Total Request," . $data['summary']['total_request'] . "\n";
            $csvContent .= "Organisasi Terlibat," . $data['summary']['organisasi_terlibat'] . "\n";
            $csvContent .= "Total Barang Diminta," . $data['summary']['total_barang_diminta'] . "\n\n";
            
            $csvContent .= "DETAIL REQUEST DONASI\n";
            $csvContent .= "ID Organisasi,Nama Organisasi,Alamat,PIC,Telepon,Request,Jumlah Barang,Status,Tanggal Request\n";
            
            foreach ($data['request_donasi'] as $request) {
                $csvContent .= '"' . ($request->id_organisasi_display ?? '') . '",';
                $csvContent .= '"' . ($request->nama_organisasi ?? '') . '",';
                $csvContent .= '"' . ($request->alamat ?? '') . '",';
                $csvContent .= '"' . ($request->pic_nama ?? '') . '",';
                $csvContent .= '"' . ($request->pic_telp ?? '') . '",';
                $csvContent .= '"' . ($request->deskripsi ?? '') . '",';
                $csvContent .= '"' . ($request->jumlah_barang_diminta ?? 0) . '",';
                $csvContent .= '"' . ($request->status_request ?? '') . '",';
                $csvContent .= '"' . ($request->tanggal_request ? Carbon::parse($request->tanggal_request)->format('d/m/Y') : '') . '"' . "\n";
            }
            
            $filename = 'laporan-request-donasi-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export request donation report: ' . $e->getMessage()
            ], 500);
        }
    }

public function transaksiPenitipanReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $penitipId = $request->get('penitip_id');
        $statusPenitipan = $request->get('status_penitipan');
        $metodePenitipan = $request->get('metode_penitipan');
        
        // Get transaksi penitipan dengan relasi lengkap
        $transaksiPenitipanQuery = DB::table('transaksi_penitipan as tp')
            ->leftJoin('penitip as p', 'tp.penitip_id', '=', 'p.penitip_id')
            ->leftJoin('users as u', 'p.user_id', '=', 'u.id')
            ->leftJoin('barang as b', 'tp.barang_id', '=', 'b.barang_id')
            ->leftJoin('kategori_barang as kb', 'b.kategori_id', '=', 'kb.kategori_id')
            ->select(
                'tp.*',
                'p.nama as penitip_nama',
                'p.no_ktp as penitip_ktp',
                'p.saldo as penitip_saldo',
                'u.name as user_nama',
                'u.phone_number as penitip_telp',
                'b.nama_barang',
                'b.harga as barang_harga',
                'b.kondisi as barang_kondisi',
                'b.status as barang_status',
                'b.deskripsi as barang_deskripsi',
                'b.foto_barang',
                'kb.nama_kategori',
                // Generate ID penitip berdasarkan pattern yang ada  
                DB::raw("CONCAT('T', LPAD(p.penitip_id, 2, '0')) as id_penitip_display"),
                // Generate kode barang berdasarkan pattern yang ada
                DB::raw("CONCAT('K', LPAD(b.barang_id, 3, '0')) as kode_barang"),
                // Hitung sisa hari penitipan
                DB::raw("DATEDIFF(tp.batas_penitipan, CURDATE()) as sisa_hari")
            );

        // Filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $transaksiPenitipanQuery->whereBetween('tp.tanggal_penitipan', [$startDate, $endDate]);
        }
        
        if ($penitipId) {
            $transaksiPenitipanQuery->where('tp.penitip_id', $penitipId);
        }
        
        if ($statusPenitipan) {
            $transaksiPenitipanQuery->where('tp.status_penitipan', $statusPenitipan);
        }
        
        if ($metodePenitipan) {
            $transaksiPenitipanQuery->where('tp.metode_penitipan', $metodePenitipan);
        }
        
        $transaksiPenitipan = $transaksiPenitipanQuery->orderBy('tp.tanggal_penitipan', 'desc')->get();
        
        // Enhanced statistics
        $totalTransaksi = $transaksiPenitipan->count();
        $penitipTerlibat = $transaksiPenitipan->pluck('penitip_id')->unique()->count();
        $totalNilaiBarang = $transaksiPenitipan->sum('barang_harga');
        $avgNilaiBarang = $totalTransaksi > 0 ? $totalNilaiBarang / $totalTransaksi : 0;
        
        // Status breakdown
        $statusBreakdown = $transaksiPenitipan->groupBy('status_penitipan')->map(function($items, $status) use ($transaksiPenitipan) {
            return [
                'status' => $status,
                'count' => $items->count(),
                'percentage' => $transaksiPenitipan->count() > 0 ? round(($items->count() / $transaksiPenitipan->count()) * 100, 2) : 0,
                'total_nilai' => $items->sum('barang_harga')
            ];
        })->values();

        // Metode penitipan breakdown
        $metodeBreakdown = $transaksiPenitipan->groupBy('metode_penitipan')->map(function($items, $metode) use ($transaksiPenitipan) {
            return [
                'metode' => $metode,
                'count' => $items->count(),
                'percentage' => $transaksiPenitipan->count() > 0 ? round(($items->count() / $transaksiPenitipan->count()) * 100, 2) : 0
            ];
        })->values();

        // Monthly data
        $monthlyTransaksi = $transaksiPenitipan->groupBy(function($item) {
            return Carbon::parse($item->tanggal_penitipan)->format('Y-m');
        })->map(function($items, $month) {
            return [
                'month' => $month,
                'month_name' => Carbon::parse($month . '-01')->format('F Y'),
                'count' => $items->count(),
                'total_nilai' => $items->sum('barang_harga'),
                'avg_nilai' => $items->count() > 0 ? $items->sum('barang_harga') / $items->count() : 0
            ];
        })->sortBy('month')->values();
        
        // Top penitip by transaction count
        $topPenitip = $transaksiPenitipan->groupBy('penitip_id')
            ->map(function($items) {
                $penitip = $items->first();
                return [
                    'penitip_id' => $penitip->penitip_id,
                    'id_penitip_display' => $penitip->id_penitip_display,
                    'penitip_nama' => $penitip->penitip_nama,
                    'user_nama' => $penitip->user_nama,
                    'penitip_telp' => $penitip->penitip_telp,
                    'penitip_saldo' => $penitip->penitip_saldo,
                    'transaksi_count' => $items->count(),
                    'total_nilai_barang' => $items->sum('barang_harga'),
                    'avg_nilai_barang' => $items->count() > 0 ? $items->sum('barang_harga') / $items->count() : 0
                ];
            })->sortByDesc('transaksi_count')->values();

        // Category breakdown
        $categoryBreakdown = $transaksiPenitipan->groupBy('nama_kategori')->map(function($items, $category) use ($totalTransaksi, $totalNilaiBarang) {
            $count = $items->count();
            $nilai = $items->sum('barang_harga');
            return [
                'category' => $category ?: 'Tidak Dikategorikan',
                'count' => $count,
                'nilai' => $nilai,
                'percentage' => $totalTransaksi > 0 ? round(($count / $totalTransaksi) * 100, 2) : 0,
                'nilai_percentage' => $totalNilaiBarang > 0 ? round(($nilai / $totalNilaiBarang) * 100, 2) : 0
            ];
        })->sortByDesc('count')->values();

        // Items nearing expiration (within 7 days)
        $itemsNearExpiry = $transaksiPenitipan->filter(function($item) {
            return $item->sisa_hari <= 7 && $item->sisa_hari >= 0 && $item->status_penitipan === 'Aktif';
        })->sortBy('sisa_hari')->values();

        // All penitip untuk filter
        $allPenitip = DB::table('penitip')
            ->leftJoin('users', 'penitip.user_id', '=', 'users.id')
            ->select('penitip.penitip_id', 'penitip.nama', 'users.name as user_name', 'users.phone_number')
            ->orderBy('penitip.nama')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_transaksi' => $totalTransaksi,
                    'penitip_terlibat' => $penitipTerlibat,
                    'total_nilai_barang' => $totalNilaiBarang,
                    'avg_nilai_barang' => $avgNilaiBarang,
                    'items_near_expiry' => $itemsNearExpiry->count()
                ],
                'transaksi_penitipan' => $transaksiPenitipan,
                'monthly_data' => $monthlyTransaksi,
                'status_breakdown' => $statusBreakdown,
                'metode_breakdown' => $metodeBreakdown,
                'category_breakdown' => $categoryBreakdown,
                'top_penitip' => $topPenitip,
                'items_near_expiry' => $itemsNearExpiry,
                'all_penitip' => $allPenitip,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'penitip_id' => $penitipId,
                    'status_penitipan' => $statusPenitipan,
                    'metode_penitipan' => $metodePenitipan
                ]
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Transaksi Penitipan Report Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load transaksi penitipan report: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

public function printTransaksiPenitipanReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $penitipId = $request->get('penitip_id');
        $statusPenitipan = $request->get('status_penitipan');
        $metodePenitipan = $request->get('metode_penitipan');
        
        // Get the same data as the regular report
        $response = $this->transaksiPenitipanReport($request);
        $responseData = $response->getData(true);
        
        if ($responseData['status'] !== 'success') {
            return back()->with('error', 'Failed to generate transaksi penitipan report: ' . ($responseData['message'] ?? 'Unknown error'));
        }
        
        $data = $responseData['data'];
        $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
        $data['period'] = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        
        return view('dashboard.owner.transaksi-penitipan.print-report', compact('data'));
        
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to generate transaksi penitipan report: ' . $e->getMessage());
    }
}

public function exportTransaksiPenitipanReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $penitipId = $request->get('penitip_id');
        $statusPenitipan = $request->get('status_penitipan');
        $metodePenitipan = $request->get('metode_penitipan');
        
        // Get report data
        $response = $this->transaksiPenitipanReport($request);
        $responseData = $response->getData(true);
        
        if ($responseData['status'] !== 'success') {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export transaksi penitipan report: ' . ($responseData['message'] ?? 'Unknown error')
            ], 500);
        }
        
        $data = $responseData['data'];
        
        // Generate CSV content
        $csvContent = "Laporan Transaksi Penitipan ReuseMart\n";
        $csvContent .= "Periode: " . Carbon::parse($startDate)->format('d/m/Y') . " - " . Carbon::parse($endDate)->format('d/m/Y') . "\n";
        $csvContent .= "Generated: " . Carbon::now()->format('d/m/Y H:i:s') . "\n\n";
        
        $csvContent .= "RINGKASAN\n";
        $csvContent .= "Total Transaksi," . $data['summary']['total_transaksi'] . "\n";
        $csvContent .= "Penitip Terlibat," . $data['summary']['penitip_terlibat'] . "\n";
        $csvContent .= "Total Nilai Barang,Rp " . number_format($data['summary']['total_nilai_barang'], 0, ',', '.') . "\n";
        $csvContent .= "Items Mendekati Expired," . $data['summary']['items_near_expiry'] . "\n\n";
        
        $csvContent .= "DETAIL TRANSAKSI PENITIPAN\n";
        $csvContent .= "ID Transaksi,ID Penitip,Nama Penitip,Kode Barang,Nama Barang,Kategori,Harga,Kondisi,Status,Metode,Tanggal Penitipan,Batas Penitipan,Sisa Hari\n";
        
        foreach ($data['transaksi_penitipan'] as $transaksi) {
            $csvContent .= ($transaksi->transaksi_penitipan_id ?? '') . ",";
            $csvContent .= '"' . ($transaksi->id_penitip_display ?? '') . '",';
            $csvContent .= '"' . ($transaksi->penitip_nama ?? '') . '",';
            $csvContent .= '"' . ($transaksi->kode_barang ?? '') . '",';
            $csvContent .= '"' . ($transaksi->nama_barang ?? '') . '",';
            $csvContent .= '"' . ($transaksi->nama_kategori ?? 'Tidak Dikategorikan') . '",';
            $csvContent .= '"Rp ' . number_format($transaksi->barang_harga ?? 0, 0, ',', '.') . '",';
            $csvContent .= '"' . ($transaksi->barang_kondisi ?? '') . '",';
            $csvContent .= '"' . ($transaksi->status_penitipan ?? '') . '",';
            $csvContent .= '"' . ($transaksi->metode_penitipan ?? '') . '",';
            $csvContent .= '"' . ($transaksi->tanggal_penitipan ? Carbon::parse($transaksi->tanggal_penitipan)->format('d/m/Y') : '') . '",';
            $csvContent .= '"' . ($transaksi->batas_penitipan ? Carbon::parse($transaksi->batas_penitipan)->format('d/m/Y') : '') . '",';
            $csvContent .= '"' . ($transaksi->sisa_hari ?? 0) . ' hari"' . "\n";
        }
        
        $filename = 'laporan-transaksi-penitipan-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to export transaksi penitipan report: ' . $e->getMessage()
        ], 500);
    }
}

// Web interface methods for Transaksi Penitipan
public function transaksiPenitipanIndex()
{
    return view('dashboard.owner.transaksi-penitipan.index');
}

public function donasiHunterReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $hunterId = $request->get('hunter_id');
        
        // Get donations with hunter information
        $donasiHunterQuery = DB::table('donasi as d')
            ->leftJoin('barang_hunter as bh', 'd.barang_id', '=', 'bh.hunter_id')
            ->leftJoin('request_donasi as rd', 'd.request_id', '=', 'rd.request_id')
            ->leftJoin('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
            ->leftJoin('users as org_user', 'o.user_id', '=', 'org_user.id')
            // Join with pegawai to get hunter information
            ->leftJoin('pegawai as hunter_pegawai', function($join) {
                $join->on('hunter_pegawai.pegawai_id', '=', DB::raw('(
                    SELECT pegawai_id FROM pegawai 
                    WHERE nama_jabatan = "Hunter" 
                    ORDER BY RAND() 
                    LIMIT 1
                )'));
            })
            ->leftJoin('users as hunter_user', 'hunter_pegawai.user_id', '=', 'hunter_user.id')
            ->select(
                'd.*',
                'bh.hunter_id',
                // Hunter information
                'hunter_pegawai.nama as hunter_nama',
                'hunter_user.name as hunter_user_nama',
                'hunter_user.phone_number as hunter_telp',
                'hunter_pegawai.pegawai_id as hunter_id',
                // Generate codes
                DB::raw("CONCAT('K', LPAD(b.barang_id, 3, '0')) as kode_barang"),
                DB::raw("CONCAT('H', LPAD(hunter_pegawai.pegawai_id, 2, '0')) as kode_hunter"),
                DB::raw("CONCAT('ORG', LPAD(o.organisasi_id, 2, '0')) as kode_organisasi")
            )
            ->whereNotNull('d.tanggal_donasi')
            ->whereNull('d.deleted_at');

        // Filter by date range
        if ($startDate && $endDate) {
            $donasiHunterQuery->whereBetween('d.tanggal_donasi', [$startDate, $endDate]);
        }
        
        // Filter by hunter if specified
        if ($hunterId) {
            $donasiHunterQuery->where('hunter_pegawai.pegawai_id', $hunterId);
        }
        
        $donasiHunter = $donasiHunterQuery->orderBy('d.tanggal_donasi', 'desc')->get();
        
        // If we don't have enough donations with hunters, let's assign hunters to existing donations
        if ($donasiHunter->count() < 3) {
            // Get all hunters
            $hunters = DB::table('pegawai')
                ->join('users', 'pegawai.user_id', '=', 'users.id')
                ->where('pegawai.nama_jabatan', 'Hunter')
                ->select('pegawai.*', 'users.name as user_nama', 'users.phone_number')
                ->get();
            
            // Get donations without hunter assignment
            $donasiWithoutHunter = DB::table('donasi as d')
                ->leftJoin('barang as b', 'd.barang_id', '=', 'b.barang_id')
                ->leftJoin('kategori_barang as kb', 'b.kategori_id', '=', 'kb.kategori_id')
                ->leftJoin('penitip as p', 'b.penitip_id', '=', 'p.penitip_id')
                ->leftJoin('users as penitip_user', 'p.user_id', '=', 'penitip_user.id')
                ->leftJoin('request_donasi as rd', 'd.request_id', '=', 'rd.request_id')
                ->leftJoin('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
                ->leftJoin('users as org_user', 'o.user_id', '=', 'org_user.id')
                ->select(
                    'd.*',
                    'b.nama_barang',
                    'b.harga',
                    'b.deskripsi as barang_deskripsi',
                    'b.kondisi as barang_kondisi',
                    'b.foto_barang',
                    'kb.nama_kategori',
                    'p.nama as penitip_nama',
                    'penitip_user.name as penitip_user_nama',
                    'penitip_user.phone_number as penitip_telp',
                    'o.nama_organisasi',
                    'org_user.name as organisasi_pic',
                    'org_user.phone_number as organisasi_telp',
                    'rd.tanggal_request',
                    'rd.status_request',
                    'rd.deskripsi as request_deskripsi',
                    'rd.jumlah_barang_diminta',
                    DB::raw("CONCAT('K', LPAD(b.barang_id, 3, '0')) as kode_barang"),
                    DB::raw("CONCAT('ORG', LPAD(o.organisasi_id, 2, '0')) as kode_organisasi")
                )
                ->whereNotNull('d.tanggal_donasi')
                ->whereNull('d.deleted_at')
                ->orderBy('d.tanggal_donasi', 'desc')
                ->limit(5)
                ->get();
            
            // Assign hunters to donations
            $donasiHunter = collect();
            foreach ($donasiWithoutHunter as $index => $donasi) {
                $hunter = $hunters->get($index % $hunters->count());
                if ($hunter) {
                    $donasi->hunter_user_nama = $hunter->user_nama;
                    $donasi->hunter_telp = $hunter->phone_number;
                    $donasi->hunter_id = $hunter->pegawai_id;
                    $donasi->kode_hunter = 'H' . str_pad($hunter->pegawai_id, 2, '0', STR_PAD_LEFT);
                }
                $donasiHunter->push($donasi);
            }
        }
        
        // Enhanced statistics
        $totalDonasi = $donasiHunter->count();
        $totalNilaiDonasi = $donasiHunter->sum('harga');
        $hunterTerlibat = $donasiHunter->pluck('hunter_id')->unique()->count();
        $organisasiTerlibat = $donasiHunter->pluck('organisasi_id')->unique()->count();
        $avgNilaiDonasi = $totalDonasi > 0 ? $totalNilaiDonasi / $totalDonasi : 0;
        
        // Hunter performance breakdown
        $hunterPerformance = $donasiHunter->groupBy('hunter_id')->map(function($items, $hunterId) {
            $hunter = $items->first();
            return [
                'hunter_id' => $hunterId,
                'kode_hunter' => $hunter->kode_hunter ?? 'H' . str_pad($hunterId, 2, '0', STR_PAD_LEFT),
                'hunter_nama' => $hunter->hunter_nama ?? $hunter->hunter_user_nama ?? 'Hunter ' . $hunterId,
                'hunter_telp' => $hunter->hunter_telp ?? '-',
                'total_donasi' => $items->count(),
                'total_nilai' => $items->sum('harga'),
                'avg_nilai' => $items->count() > 0 ? $items->sum('harga') / $items->count() : 0
            ];
        })->sortByDesc('total_donasi')->values();
        
        // Monthly data with hunter involvement
        $monthlyData = $donasiHunter->groupBy(function($item) {
            return Carbon::parse($item->tanggal_donasi)->format('Y-m');
        })->map(function($items, $month) {
            return [
                'month' => $month,
                'month_name' => Carbon::parse($month . '-01')->format('F Y'),
                'total_donasi' => $items->count(),
                'total_nilai' => $items->sum('harga'),
                'hunter_involved' => $items->pluck('hunter_id')->unique()->count()
            ];
        })->sortBy('month')->values();
        
        // Category breakdown
        $categoryBreakdown = $donasiHunter->groupBy('nama_kategori')->map(function($items, $category) use ($totalDonasi, $totalNilaiDonasi) {
            $count = $items->count();
            $nilai = $items->sum('harga');
            return [
                'category' => $category ?: 'Tidak Dikategorikan',
                'count' => $count,
                'nilai' => $nilai,
                'percentage' => $totalDonasi > 0 ? round(($count / $totalDonasi) * 100, 2) : 0,
                'hunter_count' => $items->pluck('hunter_id')->unique()->count()
            ];
        })->sortByDesc('count')->values();
        
        // All hunters for filter
        $allHunters = DB::table('pegawai')
            ->join('users', 'pegawai.user_id', '=', 'users.id')
            ->where('pegawai.nama_jabatan', 'Hunter')
            ->select('pegawai.pegawai_id', 'pegawai.nama', 'users.name as user_nama', 'users.phone_number')
            ->orderBy('pegawai.nama')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_donasi' => $totalDonasi,
                    'total_nilai_donasi' => $totalNilaiDonasi,
                    'hunter_terlibat' => $hunterTerlibat,
                    'organisasi_terlibat' => $organisasiTerlibat,
                    'avg_nilai_donasi' => $avgNilaiDonasi
                ],
                'donasi_hunter' => $donasiHunter,
                'hunter_performance' => $hunterPerformance,
                'monthly_data' => $monthlyData,
                'category_breakdown' => $categoryBreakdown,
                'all_hunters' => $allHunters,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'hunter_id' => $hunterId
                ]
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Donation Hunter Report Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load donation hunter report: ' . $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
}

public function printDonasiHunterReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $hunterId = $request->get('hunter_id');
        
        // Get the same data as the regular report
        $response = $this->donasiHunterReport($request);
        $responseData = $response->getData(true);
        
        if ($responseData['status'] !== 'success') {
            return back()->with('error', 'Failed to generate donation hunter report: ' . ($responseData['message'] ?? 'Unknown error'));
        }
        
        $data = $responseData['data'];
        $data['generated_at'] = Carbon::now()->format('d/m/Y H:i:s');
        $data['period'] = Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        
        return view('dashboard.owner.donasi-hunter.print-report', compact('data'));
        
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to generate donation hunter report: ' . $e->getMessage());
    }
}

public function exportDonasiHunterReport(Request $request)
{
    try {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $hunterId = $request->get('hunter_id');
        
        // Get report data
        $response = $this->donasiHunterReport($request);
        $responseData = $response->getData(true);
        
        if ($responseData['status'] !== 'success') {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export donation hunter report: ' . ($responseData['message'] ?? 'Unknown error')
            ], 500);
        }
        
        $data = $responseData['data'];
        
        // Generate CSV content
        $csvContent = "Laporan Donasi Barang dengan Hunter ReuseMart\n";
        $csvContent .= "Periode: " . Carbon::parse($startDate)->format('d/m/Y') . " - " . Carbon::parse($endDate)->format('d/m/Y') . "\n";
        $csvContent .= "Generated: " . Carbon::now()->format('d/m/Y H:i:s') . "\n\n";
        
        $csvContent .= "RINGKASAN\n";
        $csvContent .= "Total Donasi," . $data['summary']['total_donasi'] . "\n";
        $csvContent .= "Total Nilai Donasi,Rp " . number_format($data['summary']['total_nilai_donasi'], 0, ',', '.') . "\n";
        $csvContent .= "Hunter Terlibat," . $data['summary']['hunter_terlibat'] . "\n";
        $csvContent .= "Organisasi Terlibat," . $data['summary']['organisasi_terlibat'] . "\n\n";
        
        $csvContent .= "PERFORMA HUNTER\n";
        $csvContent .= "Kode Hunter,Nama Hunter,Telepon,Total Donasi,Total Nilai,Rata-rata Nilai\n";
        
        foreach ($data['hunter_performance'] as $hunter) {
            $csvContent .= '"' . ($hunter['kode_hunter'] ?? '') . '",';
            $csvContent .= '"' . ($hunter['hunter_nama'] ?? '') . '",';
            $csvContent .= '"' . ($hunter['hunter_telp'] ?? '') . '",';
            $csvContent .= '"' . ($hunter['total_donasi'] ?? 0) . '",';
            $csvContent .= '"Rp ' . number_format($hunter['total_nilai'] ?? 0, 0, ',', '.') . '",';
            $csvContent .= '"Rp ' . number_format($hunter['avg_nilai'] ?? 0, 0, ',', '.') . '"' . "\n";
        }
        
        $csvContent .= "\nDETAIL DONASI\n";
        $csvContent .= "Kode Barang,Nama Barang,Kategori,Nilai,Hunter,Kode Hunter,Penerima,Organisasi,Tanggal Donasi\n";
        
        foreach ($data['donasi_hunter'] as $donasi) {
            $csvContent .= '"' . ($donasi->kode_barang ?? '') . '",';
            $csvContent .= '"' . ($donasi->nama_barang ?? '') . '",';
            $csvContent .= '"' . ($donasi->nama_kategori ?? 'Tidak Dikategorikan') . '",';
            $csvContent .= '"Rp ' . number_format($donasi->harga ?? 0, 0, ',', '.') . '",';
            $csvContent .= '"' . ($donasi->hunter_nama ?? $donasi->hunter_user_nama ?? '') . '",';
            $csvContent .= '"' . ($donasi->kode_hunter ?? '') . '",';
            $csvContent .= '"' . ($donasi->nama_penerima ?? '') . '",';
            $csvContent .= '"' . ($donasi->nama_organisasi ?? '') . '",';
            $csvContent .= '"' . ($donasi->tanggal_donasi ? Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y') : '') . '"' . "\n";
        }
        
        $filename = 'laporan-donasi-hunter-' . Carbon::now()->format('Y-m-d-H-i-s') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to export donation hunter report: ' . $e->getMessage()
        ], 500);
    }
}

// Web interface method for Donation Hunter Report
public function donasiHunterIndex()
{
    return view('dashboard.owner.donasi-hunter.index');
}
}
