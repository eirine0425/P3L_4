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
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function commissionReport(Request $request)
    {
        try {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
            
            // Get commission data with all required fields
            $commissions = DB::table('barang')
                ->select([
                    'barang.barang_id as kode_produk',
                    'barang.nama_barang',
                    'barang.harga as harga_jual',
                    'barang.tanggal_penitipan as tanggal_masuk',
                    'transaksi.tanggal_pesan as tanggal_laku',
                    'kategori_barang.nama_kategori',
                    'penitip.nama as penitip_nama',
                    'users.name as penitip_user_name',
                    'pegawai.nama as pegawai_nama',
                    'komisi.persentase',
                    'komisi.nominal_komisi as komisi_reusemart',
                    'transaksi.total_harga as transaksi_total',
                    // Calculate hunter commission (assume 5% of selling price for hunters)
                    DB::raw('CASE WHEN pegawai.nama IS NOT NULL THEN ROUND(barang.harga * 0.05) ELSE 0 END as komisi_hunter'),
                    // Calculate consignor bonus (assume 2% of selling price if sold within 7 days)
                    DB::raw('CASE WHEN DATEDIFF(transaksi.tanggal_pesan, barang.tanggal_penitipan) <= 7 THEN ROUND(barang.harga * 0.02) ELSE 0 END as bonus_penitip')
                ])
                ->leftJoin('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
                ->leftJoin('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
                ->leftJoin('komisi', 'barang.barang_id', '=', 'komisi.barang_id')
                ->leftJoin('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                ->leftJoin('penitip', 'barang.penitip_id', '=', 'penitip.penitip_id')
                ->leftJoin('users', 'penitip.user_id', '=', 'users.id')
                ->leftJoin('pegawai', 'komisi.pegawai_id', '=', 'pegawai.pegawai_id')
                ->where('transaksi.status_transaksi', 'selesai')
                ->whereMonth('transaksi.tanggal_pesan', $month)
                ->whereYear('transaksi.tanggal_pesan', $year)
                ->orderBy('transaksi.tanggal_pesan', 'desc')
                ->get();

            // Calculate totals
            $totalHunterCommission = $commissions->sum('komisi_hunter');
            $totalReuseMartCommission = $commissions->sum('komisi_reusemart');
            $totalConsignorBonus = $commissions->sum('bonus_penitip');
            $totalSales = $commissions->sum('harga_jual');

            // Summary data
            $summary = [
                'total_hunter_commission' => $totalHunterCommission,
                'total_reusemart_commission' => $totalReuseMartCommission,
                'total_consignor_bonus' => $totalConsignorBonus,
                'total_sales' => $totalSales,
                'total_transactions' => $commissions->count(),
                'month_name' => Carbon::createFromDate($year, $month, 1)->format('F'),
                'month_number' => $month,
                'year' => $year,
                'generated_at' => Carbon::now()->format('d F Y')
            ];

            if ($request->get('print') == 'true') {
                return view('dashboard.owner.print-commission-report', compact('commissions', 'summary'));
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'commissions' => $commissions,
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate commission report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stockReport(Request $request)
    {
        try {
            // Get warehouse stock data with proper relationships
            $stockItems = DB::table('barang')
                ->select([
                    'barang.barang_id as kode_produk',
                    'barang.nama_barang',
                    'penitip.penitip_id as id_penitip',
                    'penitip.nama as nama_penitip',
                    'users.name as nama_penitip_user',
                    'barang.tanggal_penitipan as tanggal_masuk',
                    'barang.harga',
                    'barang.status',
                    'barang.kondisi',
                    'kategori_barang.nama_kategori',
                    // Check for extension from transaksi_penitipan - using status_perpanjangan instead of perpanjangan_count
                    DB::raw('CASE WHEN transaksi_penitipan.status_perpanjangan = 1 THEN "Ya" ELSE "Tidak" END as perpanjangan'),
                    // Hunter information (assuming hunter data is stored in pegawai table)
                    'pegawai.pegawai_id as id_hunter',
                    'pegawai.nama as nama_hunter'
                ])
                ->leftJoin('penitip', 'barang.penitip_id', '=', 'penitip.penitip_id')
                ->leftJoin('users', 'penitip.user_id', '=', 'users.id')
                ->leftJoin('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                ->leftJoin('transaksi_penitipan', 'barang.barang_id', '=', 'transaksi_penitipan.barang_id')
                ->leftJoin('komisi', 'barang.barang_id', '=', 'komisi.barang_id')
                ->leftJoin('pegawai', 'komisi.pegawai_id', '=', 'pegawai.pegawai_id')
                ->where('barang.status', '=', 'belum_terjual')
                ->orderBy('barang.tanggal_penitipan', 'desc')
                ->get();

            // Calculate summary data
            $summary = [
                'total_items' => $stockItems->count(),
                'total_value' => $stockItems->sum('harga'),
                'items_with_extension' => $stockItems->where('perpanjangan', 'Ya')->count(),
                'items_from_hunters' => $stockItems->whereNotNull('id_hunter')->count(),
                'generated_at' => Carbon::now()->format('d F Y')
            ];

            if ($request->get('print') == 'true') {
                return view('dashboard.owner.print-stock-report', compact('stockItems', 'summary'));
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stock_items' => $stockItems,
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate stock report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hunterCommissionReport(Request $request)
    {
        try {
            $month = $request->get('month', date('m'));
            $year = $request->get('year', date('Y'));
        
            // Get hunter commission data with detailed information
            $hunterCommissions = DB::table('komisi')
                ->select([
                    'pegawai.pegawai_id',
                    'pegawai.nama as nama_hunter',
                    'pegawai.nama_jabatan',
                    'pegawai.tanggal_bergabung as tanggal_bergabung_pegawai',
                    'users.name as hunter_user_name',
                    'users.email as hunter_email',
                    'users.created_at as tanggal_bergabung',
                    DB::raw('COUNT(*) as total_barang'),
                    DB::raw('SUM(barang.harga) as total_penjualan'),
                    DB::raw('SUM(komisi.nominal_komisi) as total_komisi'),
                    DB::raw('AVG(komisi.persentase) as rata_rata_persentase'),
                    DB::raw('MIN(transaksi.tanggal_pesan) as tanggal_pertama'),
                    DB::raw('MAX(transaksi.tanggal_pesan) as tanggal_terakhir')
                ])
                ->join('pegawai', 'komisi.pegawai_id', '=', 'pegawai.pegawai_id')
                ->join('users', 'pegawai.user_id', '=', 'users.id')
                ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
                ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
                ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
                ->where('transaksi.status_transaksi', 'selesai')
                ->whereMonth('transaksi.tanggal_pesan', $month)
                ->whereYear('transaksi.tanggal_pesan', $year)
                ->whereNotNull('komisi.pegawai_id')
                ->groupBy('pegawai.pegawai_id', 'pegawai.nama', 'pegawai.nama_jabatan', 'pegawai.tanggal_bergabung', 'users.name', 'users.email', 'users.created_at')
                ->orderBy('total_komisi', 'desc')
                ->get();

            // Get detailed transactions for each hunter
            $hunterDetails = [];
            foreach ($hunterCommissions as $hunter) {
                $details = DB::table('komisi')
                    ->select([
                        'barang.barang_id as kode_produk',
                        'barang.nama_barang',
                        'barang.harga as harga_jual',
                        'barang.tanggal_penitipan as tanggal_masuk',
                        'transaksi.tanggal_pesan as tanggal_laku',
                        'kategori_barang.nama_kategori',
                        'penitip.nama as penitip_nama',
                        'komisi.persentase',
                        'komisi.nominal_komisi',
                        DB::raw('DATEDIFF(transaksi.tanggal_pesan, barang.tanggal_penitipan) as hari_terjual'),
                        DB::raw('ROUND(barang.harga * 0.05) as komisi_hunter')
                    ])
                    ->join('pegawai', 'komisi.pegawai_id', '=', 'pegawai.pegawai_id')
                    ->join('barang', 'komisi.barang_id', '=', 'barang.barang_id')
                    ->join('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
                    ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
                    ->leftJoin('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
                    ->leftJoin('penitip', 'barang.penitip_id', '=', 'penitip.penitip_id')
                    ->where('pegawai.pegawai_id', $hunter->pegawai_id)
                    ->where('transaksi.status_transaksi', 'selesai')
                    ->whereMonth('transaksi.tanggal_pesan', $month)
                    ->whereYear('transaksi.tanggal_pesan', $year)
                    ->orderBy('transaksi.tanggal_pesan', 'desc')
                    ->get();
            
                $hunterDetails[$hunter->pegawai_id] = $details;
            }

            // Calculate summary
            $summary = [
                'total_hunters' => $hunterCommissions->count(),
                'active_hunters' => $hunterCommissions->where('total_barang', '>', 0)->count(),
                'total_items_sold' => $hunterCommissions->sum('total_barang'),
                'total_sales_value' => $hunterCommissions->sum('total_penjualan'),
                'total_commission_paid' => $hunterCommissions->sum('total_komisi'),
                'average_commission_rate' => $hunterCommissions->avg('rata_rata_persentase'),
                'avg_commission_per_hunter' => $hunterCommissions->count() > 0 ? $hunterCommissions->sum('total_komisi') / $hunterCommissions->count() : 0,
                'month_name' => Carbon::createFromDate($year, $month, 1)->format('F'),
                'month_number' => $month,
                'year' => $year,
                'generated_at' => Carbon::now()->format('d F Y H:i:s')
            ];

            foreach ($hunterCommissions as $hunter) {
                $hunter->total_items = $hunter->total_barang;
                $hunter->total_sales_value = $hunter->total_penjualan;
                $hunter->total_commission = $hunter->total_komisi;
                $hunter->avg_item_price = $hunter->total_barang > 0 ? $hunter->total_penjualan / $hunter->total_barang : 0;
                $hunter->first_sale_date = $hunter->tanggal_pertama;
                $hunter->last_sale_date = $hunter->tanggal_terakhir;
                $hunter->hunter_name = $hunter->nama_hunter;
            }

            if ($request->get('print') == 'true') {
                return view('dashboard.owner.print-hunter-commission-report', compact('hunterCommissions', 'hunterDetails', 'summary'));
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'hunter_commissions' => $hunterCommissions,
                    'hunter_details' => $hunterDetails,
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate hunter commission report: ' . $e->getMessage()
            ], 500);
        }
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

    public function monthlySalesReport(Request $request)
    {
        try {
            $year = $request->get('year', date('Y'));
            
            // Get monthly sales data
            $monthlySales = DB::table('transaksi')
                ->select(
                    DB::raw('MONTH(tanggal_pesan) as month'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(total_harga) as total_sales'),
                    DB::raw('COUNT(DISTINCT detail_transaksi.barang_id) as total_items')
                )
                ->join('detail_transaksi', 'transaksi.transaksi_id', '=', 'detail_transaksi.transaksi_id')
                ->where('status_transaksi', 'selesai')
                ->whereYear('tanggal_pesan', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            
            // Format data for all months (including months with no sales)
            $formattedData = [];
            $totalItems = 0;
            $totalSales = 0;
            
            for ($i = 1; $i <= 12; $i++) {
                $monthData = $monthlySales->where('month', $i)->first();
                
                $formattedData[$i] = [
                    'month' => $i,
                    'month_name' => date('F', mktime(0, 0, 0, $i, 1)),
                    'total_items' => $monthData ? $monthData->total_items : 0,
                    'total_sales' => $monthData ? $monthData->total_sales : 0
                ];
                
                $totalItems += $formattedData[$i]['total_items'];
                $totalSales += $formattedData[$i]['total_sales'];
            }
            
            if ($request->get('print') == 'true') {
                return view('dashboard.owner.print-monthly-sales', [
                    'data' => $formattedData,
                    'year' => $year,
                    'total_items' => $totalItems,
                    'total_sales' => $totalSales,
                    'print_date' => Carbon::now()->format('j F Y')
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'monthly_sales' => $formattedData,
                    'year' => $year,
                    'total_items' => $totalItems,
                    'total_sales' => $totalSales
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate monthly sales report: ' . $e->getMessage()
            ], 500);
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
}
