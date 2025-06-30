<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerReportController extends Controller
{
    /**
     * Show form for sales report by category with hunter
     */
    public function salesReportByCategoryWithHunterForm()
    {
        try {
            // Get all categories for reference
            $categories = KategoriBarang::orderBy('nama_kategori')->get();
            
            // Get available years from transaction data
            $availableYears = DB::table('transaksi')
                ->selectRaw('YEAR(tanggal_pesan) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
            
            if ($availableYears->isEmpty()) {
                $availableYears = collect([date('Y')]);
            }
            
            return view('dashboard.owner.reports.sales-category-hunter-form', compact('categories', 'availableYears'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }
    
    /**
     * Process and display sales report by category with hunter
     */
    public function salesReportByCategoryWithHunter(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_products' => 'required|integer|min:1|max:100'
        ]);
        
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $minProducts = $request->min_products;
            
            // Get sales data by category with hunter information
            $salesByCategory = $this->getSalesDataWithHunter($startDate, $endDate, $minProducts);
            
            // Calculate summary
            $summary = $this->calculateSummary($salesByCategory);
            
            // Prepare data for view
            $data = [
                'sales_by_category' => $salesByCategory,
                'summary' => $summary,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'min_products' => $minProducts,
                    'period_text' => Carbon::parse($startDate)->format('d F Y') . ' - ' . Carbon::parse($endDate)->format('d F Y')
                ]
            ];
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            return view('dashboard.owner.reports.sales-category-hunter-result', $data);
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating report: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error generating report: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Download PDF report for sales by category with hunter
     */
    public function salesReportByCategoryWithHunterPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_products' => 'required|integer|min:1|max:100'
        ]);
        
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $minProducts = $request->min_products;
            
            // Get the same data as the display report
            $salesByCategory = $this->getSalesDataWithHunter($startDate, $endDate, $minProducts);
            $summary = $this->calculateSummary($salesByCategory);
            
            // Prepare data for PDF
            $data = [
                'title' => 'LAPORAN PENJUALAN PER KATEGORI DENGAN HUNTER',
                'company_name' => 'ReUse Mart',
                'company_address' => 'Jl. Green Eco Park No. 456 Yogyakarta',
                'period_start' => Carbon::parse($startDate)->format('d F Y'),
                'period_end' => Carbon::parse($endDate)->format('d F Y'),
                'min_products' => $minProducts,
                'sales_by_category' => $salesByCategory,
                'summary' => $summary,
                'generated_at' => Carbon::now()->format('d F Y H:i'),
                'generated_by' => auth()->user()->name ?? 'System'
            ];
            
            $pdf = PDF::loadView('pdf.sales-report-category-hunter', $data);
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'laporan-penjualan-kategori-hunter-' . Carbon::now()->format('Y-m-d-H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error generating sales report with hunter PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id() ?? 'guest'
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating PDF: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sales data by category with hunter information
     */
    private function getSalesDataWithHunter($startDate, $endDate, $minProducts)
    {
        return DB::table('kategori_barang')
            ->leftJoin('barang', 'kategori_barang.kategori_id', '=', 'barang.kategori_id')
            ->leftJoin('detail_transaksi', 'barang.barang_id', '=', 'detail_transaksi.barang_id')
            ->leftJoin('transaksi', function($join) use ($startDate, $endDate) {
                $join->on('detail_transaksi.transaksi_id', '=', 'transaksi.transaksi_id')
                     ->where('transaksi.status_transaksi', 'selesai')
                     ->whereBetween('transaksi.tanggal_pesan', [$startDate, $endDate]);
            })
            ->leftJoin('pegawai as hunter', function($join) {
                $join->on('barang.pegawai_pickup_id', '=', 'hunter.pegawai_id');
            })
            ->leftJoin('users as hunter_user', 'hunter.user_id', '=', 'hunter_user.id')
            ->leftJoin('roles as hunter_role', 'hunter_user.role_id', '=', 'hunter_role.role_id')
            ->select(
                'kategori_barang.kategori_id',
                'kategori_barang.nama_kategori',
                DB::raw('COUNT(DISTINCT barang.barang_id) as total_products'),
                DB::raw('COUNT(DISTINCT CASE WHEN detail_transaksi.barang_id IS NOT NULL THEN barang.barang_id END) as items_sold'),
                DB::raw('COUNT(DISTINCT CASE WHEN detail_transaksi.barang_id IS NULL AND barang.barang_id IS NOT NULL THEN barang.barang_id END) as items_unsold'),
                DB::raw('SUM(CASE WHEN detail_transaksi.harga IS NOT NULL THEN detail_transaksi.harga ELSE 0 END) as total_revenue'),
                DB::raw('AVG(CASE WHEN detail_transaksi.harga IS NOT NULL THEN detail_transaksi.harga ELSE NULL END) as average_price'),
                DB::raw('COUNT(DISTINCT hunter.pegawai_id) as hunter_count'),
                DB::raw('GROUP_CONCAT(DISTINCT hunter_user.name SEPARATOR ", ") as hunter_names')
            )
            ->where('hunter_role.nama_role', 'hunter') // Only categories with hunters
            ->whereNotNull('hunter.pegawai_id')
            ->groupBy('kategori_barang.kategori_id', 'kategori_barang.nama_kategori')
            ->havingRaw('COUNT(DISTINCT barang.barang_id) >= ?', [$minProducts])
            ->orderBy('total_revenue', 'desc')
            ->get();
    }
    
    /**
     * Calculate summary data
     */
    private function calculateSummary($salesByCategory)
    {
        $totalProducts = $salesByCategory->sum('total_products');
        $totalSold = $salesByCategory->sum('items_sold');
        $totalUnsold = $salesByCategory->sum('items_unsold');
        $totalRevenue = $salesByCategory->sum('total_revenue');
        $totalHunters = $salesByCategory->sum('hunter_count');
        
        return [
            'total_categories' => $salesByCategory->count(),
            'total_products' => $totalProducts,
            'total_sold' => $totalSold,
            'total_unsold' => $totalUnsold,
            'total_revenue' => $totalRevenue,
            'total_hunters' => $totalHunters,
            'sales_percentage' => $totalProducts > 0 ? round(($totalSold / $totalProducts) * 100, 1) : 0,
            'avg_products_per_category' => $salesByCategory->count() > 0 ? round($totalProducts / $salesByCategory->count(), 1) : 0,
            'avg_hunters_per_category' => $salesByCategory->count() > 0 ? round($totalHunters / $salesByCategory->count(), 1) : 0
        ];
    }
}
