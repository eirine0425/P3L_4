<?php

namespace App\Http\Controllers\Dashboard\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RequestDonasiController extends Controller
{
    public function reportData(Request $request)
    {
        try {
            $startDate = $request->get('start_date', date('Y-m-01'));
            $endDate = $request->get('end_date', date('Y-m-t'));
            $statusFilter = $request->get('status', '');

            Log::info('RequestDonasiController@reportData called with params:', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $statusFilter
            ]);

            // ✅ PERBAIKAN: Query yang lebih robust dengan error checking
            $query = DB::table('request_donasi as rd')
                ->leftJoin('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
                ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
                ->select([
                    'rd.request_id as id',
                    'rd.organisasi_id',
                    'o.nama_organisasi',
                    'o.alamat',
                    'rd.deskripsi as request_description',
                    'rd.tanggal_request',
                    'rd.tanggal_donasi',
                    'rd.status_request as status',
                    'rd.jumlah_barang_diminta as jumlah_barang',
                    'u.name as nama_penerima',
                    'u.phone_number as telepon_penerima',
                    'rd.created_at',
                    'rd.updated_at'
                ]);

            // ✅ PERBAIKAN: Filter yang lebih fleksibel
            if ($startDate && $endDate) {
                $query->whereBetween('rd.tanggal_request', [$startDate, $endDate]);
            }

            if ($statusFilter && $statusFilter !== '' && $statusFilter !== 'all') {
                $query->where('rd.status_request', $statusFilter);
            }

            // ✅ PERBAIKAN: Debug query sebelum eksekusi
            $sqlQuery = $query->toSql();
            $bindings = $query->getBindings();
            Log::info('SQL Query:', ['query' => $sqlQuery, 'bindings' => $bindings]);

            $requests = $query->orderBy('rd.tanggal_request', 'desc')->get();

            Log::info('Query executed, found ' . $requests->count() . ' records');

            // ✅ PERBAIKAN: Hitung summary dengan query terpisah untuk akurasi
            $summaryQuery = DB::table('request_donasi as rd')
                ->leftJoin('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id');

            if ($startDate && $endDate) {
                $summaryQuery->whereBetween('rd.tanggal_request', [$startDate, $endDate]);
            }

            $allRequests = $summaryQuery->get();

            $summary = [
                'total_request' => $allRequests->count(),
                'pending_request' => $allRequests->where('status_request', 'menunggu')->count(),
                'fulfilled_request' => $allRequests->where('status_request', 'disetujui')->count(),
                'rejected_request' => $allRequests->where('status_request', 'ditolak')->count(),
                'organisasi_aktif' => $allRequests->pluck('organisasi_id')->unique()->filter()->count(),
            ];

            // ✅ PERBAIKAN: Format data dengan null safety yang lebih baik
            $formattedRequests = $requests->map(function ($item) {
                return [
                    'id' => $item->id,
                    'organisasi_id' => $item->organisasi_id ? 'ORG' . str_pad($item->organisasi_id, 2, '0', STR_PAD_LEFT) : 'N/A',
                    'nama_organisasi' => $item->nama_organisasi ?? 'Organisasi Tidak Ditemukan',
                    'alamat' => $item->alamat ?? 'Alamat tidak tersedia',
                    'request_description' => $item->request_description ?? 'Tidak ada deskripsi',
                    'tanggal_request' => $item->tanggal_request,
                    'tanggal_donasi' => $item->tanggal_donasi,
                    'status' => $item->status ?? 'menunggu',
                    'jumlah_barang' => $item->jumlah_barang ?? 0,
                    'nama_penerima' => $item->nama_penerima ?? 'Tidak diketahui',
                    'telepon_penerima' => $item->telepon_penerima ?? 'Tidak diketahui',
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at
                ];
            });

            Log::info('Returning ' . $formattedRequests->count() . ' requests');

            // ✅ PERBAIKAN: Response yang lebih informatif
            return response()->json([
                'status' => 'success',
                'message' => 'Data request berhasil dimuat',
                'data' => [
                    'summary' => $summary,
                    'requests' => $formattedRequests,
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status_filter' => $statusFilter
                    ],
                    'debug' => [
                        'total_found' => $requests->count(),
                        'query_params' => $request->all(),
                        'sql_query' => $sqlQuery,
                        'has_data' => $requests->count() > 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in RequestDonasiController@reportData: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                'data' => null,
                'debug' => [
                    'error_line' => $e->getLine(),
                    'error_file' => $e->getFile(),
                    'request_params' => $request->all()
                ]
            ], 500);
        }
    }

    // ✅ PERBAIKAN: Method untuk test koneksi database
    public function testDatabaseConnection()
    {
        try {
            // Test basic table existence
            $requestCount = DB::table('request_donasi')->count();
            $organisasiCount = DB::table('organisasi')->count();
            
            // Test join query
            $joinTest = DB::table('request_donasi as rd')
                ->leftJoin('organisasi as o', 'rd.organisasi_id', '=', 'o.organisasi_id')
                ->select('rd.request_id', 'o.nama_organisasi')
                ->limit(1)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'request_donasi_count' => $requestCount,
                    'organisasi_count' => $organisasiCount,
                    'join_test' => $joinTest->count() > 0 ? 'SUCCESS' : 'NO_DATA',
                    'sample_data' => $joinTest->first()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}