<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Transaksi;
use App\Models\Pengiriman;
use App\Models\Penitip;
use App\Models\TransaksiPenitipan;
use Illuminate\Support\Facades\DB;

class DashboardWarehouseController extends Controller
{
    /**
     * Display warehouse dashboard
     */
    public function index()
    {
        try {
            // Get statistics
            $totalItems = Barang::count();
            $activeItems = Barang::where('status', 'belum_terjual')->count();
            $soldItems = Barang::where('status', 'terjual')->count();
            $soldOutItems = Barang::where('status', 'sold_out')->count();
            
            // Get recent items (last 10)
            $recentItems = Barang::with(['kategori', 'penitip.user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Get items by status for chart
            $itemsByStatus = Barang::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
            
            // Get items by category for chart
            $itemsByCategory = KategoriBarang::select('kategori_barangs.nama_kategori', DB::raw('count(barangs.barang_id) as total'))
                ->leftJoin('barangs', 'kategori_barangs.id', '=', 'barangs.kategori_id')
                ->groupBy('kategori_barangs.id', 'kategori_barangs.nama_kategori')
                ->having('total', '>', 0)
                ->get();
            
            return view('dashboard.warehouse.index', compact(
                'totalItems',
                'activeItems', 
                'soldItems',
                'soldOutItems',
                'recentItems',
                'itemsByStatus',
                'itemsByCategory'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display inventory management page
     */
    public function inventory(Request $request)
    {
        try {
            $query = Barang::with(['kategori', 'penitip.user']);
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            // Filter by category
            if ($request->has('category') && $request->category != '') {
                $query->where('kategori_id', $request->category);
            }
            
            // Search by name
            if ($request->has('search') && $request->search != '') {
                $query->where('nama_barang', 'like', '%' . $request->search . '%');
            }
            
            // Sort
            if ($request->has('sort')) {
                switch ($request->sort) {
                    case 'name_asc':
                        $query->orderBy('nama_barang', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('nama_barang', 'desc');
                        break;
                    case 'price_asc':
                        $query->orderBy('harga', 'asc');
                        break;
                    case 'price_desc':
                        $query->orderBy('harga', 'desc');
                        break;
                    case 'date_asc':
                        $query->orderBy('created_at', 'asc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            $items = $query->paginate(15)->appends($request->query());
            $categories = KategoriBarang::all();
            
            return view('dashboard.warehouse.inventory', compact('items', 'categories'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display shipments page
     */
    public function shipments(Request $request)
    {
        try {
            $query = Pengiriman::with(['transaksi.user']);
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            $shipments = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->appends($request->query());
            
            return view('dashboard.warehouse.shipments', compact('shipments'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display transactions list
     */
    public function transactionsList(Request $request)
    {
        try {
            $query = Transaksi::with(['user', 'details.barang']);
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->appends($request->query());
            
            return view('dashboard.warehouse.transactions', compact('transactions'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show create consignment item form
     */
    public function createConsignmentItem()
    {
        try {
            $categories = KategoriBarang::all();
            $penitips = Penitip::with('user')->get();
            
            return view('dashboard.warehouse.consignment.create', compact('categories', 'penitips'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store consignment item
     */
    public function storeConsignmentItem(Request $request)
    {
        try {
            $request->validate([
                'nama_barang' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'harga' => 'required|numeric|min:0',
                'kategori_id' => 'required|exists:kategori_barangs,id',
                'penitip_id' => 'required|exists:penitips,id',
                'kondisi' => 'required|in:baru,bekas_baik,bekas_rusak',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $data = $request->all();
            $data['status'] = 'belum_terjual';
            
            // Handle file upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/barang'), $filename);
                $data['foto'] = 'uploads/barang/' . $filename;
            }

            Barang::create($data);

            return redirect()->route('dashboard.warehouse.inventory')
                ->with('success', 'Barang titipan berhasil ditambahkan');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show item details
     */
    public function showItem($id)
    {
        try {
            $item = Barang::with(['kategori', 'penitip.user'])->findOrFail($id);
            
            return view('dashboard.warehouse.item-detail', compact('item'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Barang tidak ditemukan');
        }
    }

    /**
     * Update item status
     */
    public function updateItemStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:belum_terjual,terjual,sold_out'
            ]);

            $item = Barang::findOrFail($id);
            $item->update(['status' => $request->status]);

            return back()->with('success', 'Status barang berhasil diperbarui');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show shipment details
     */
    public function showShipment($id)
    {
        try {
            $shipment = Pengiriman::with(['transaksi.user', 'transaksi.details.barang'])
                ->findOrFail($id);
            
            return view('dashboard.warehouse.shipment-detail', compact('shipment'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Pengiriman tidak ditemukan');
        }
    }

    /**
     * Update shipment status
     */
    public function updateShipmentStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
            ]);

            $shipment = Pengiriman::findOrFail($id);
            $shipment->update(['status' => $request->status]);

            return back()->with('success', 'Status pengiriman berhasil diperbarui');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Create shipping schedule
     */
    public function createShippingSchedule(Request $request, $id)
    {
        try {
            $transaction = Transaksi::findOrFail($id);
            
            // Create shipping record
            Pengiriman::create([
                'transaksi_id' => $transaction->id,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'status' => 'pending'
            ]);

            return back()->with('success', 'Jadwal pengiriman berhasil dibuat');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Create pickup schedule
     */
    public function createPickupSchedule(Request $request, $id)
    {
        try {
            // Implementation for pickup schedule
            return back()->with('success', 'Jadwal penjemputan berhasil dibuat');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate sales note
     */
    public function generateSalesNote($id)
    {
        try {
            $transaction = Transaksi::with(['user', 'details.barang'])->findOrFail($id);
            
            // Generate PDF or return view
            return view('pdf.notaPenjualan', compact('transaction'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Confirm item received
     */
    public function confirmItemReceived(Request $request, $id)
    {
        try {
            $transaction = Transaksi::findOrFail($id);
            $transaction->update(['status' => 'confirmed']);

            return back()->with('success', 'Penerimaan barang berhasil dikonfirmasi');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update transaction status
     */
    public function updateTransactionStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
            ]);

            $transaction = Transaksi::findOrFail($id);
            $transaction->update(['status' => $request->status]);

            return back()->with('success', 'Status transaksi berhasil diperbarui');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
