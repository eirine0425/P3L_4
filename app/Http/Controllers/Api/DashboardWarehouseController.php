<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pengiriman;

class DashboardWarehouseController extends Controller
{
    public function transactionsList(Request $request)
    {
        $query = Transaksi::with(['pembeli.user', 'detailTransaksi.barang', 'pengiriman'])
            ->where('status_transaksi', 'Lunas');
        
        // Filter berdasarkan status pengiriman
        if ($request->has('shipping_status') && $request->shipping_status != '') {
            $query->whereHas('pengiriman', function($q) use ($request) {
                $q->where('status_pengiriman', $request->shipping_status);
            });
        }
        
        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaksi_id', 'like', "%{$search}%")
                  ->orWhereHas('pembeli.user', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('dashboard.warehouse.transactions', compact('transactions'));
    }

    public function createShippingSchedule(Request $request, $transactionId)
    {
        $request->validate([
            'tanggal_pengiriman' => 'required|date',
            'metode_pengiriman' => 'required|string',
            'alamat_pengiriman' => 'required|string',
            'catatan' => 'nullable|string'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Create or update pengiriman record
        $pengiriman = Pengiriman::updateOrCreate(
            ['transaksi_id' => $transactionId],
            [
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'metode_pengiriman' => $request->metode_pengiriman,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'status_pengiriman' => 'Dijadwalkan',
                'catatan' => $request->catatan
            ]
        );
        
        return redirect()->back()->with('success', 'Jadwal pengiriman berhasil dibuat.');
    }

    public function createPickupSchedule(Request $request, $transactionId)
    {
        $request->validate([
            'tanggal_pengambilan' => 'required|date',
            'jam_pengambilan' => 'required|string',
            'alamat_pengambilan' => 'required|string',
            'catatan' => 'nullable|string'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Create or update pengiriman record for pickup
        $pengiriman = Pengiriman::updateOrCreate(
            ['transaksi_id' => $transactionId],
            [
                'tanggal_pengiriman' => $request->tanggal_pengambilan,
                'jam_pengiriman' => $request->jam_pengambilan,
                'metode_pengiriman' => 'Pickup',
                'alamat_pengiriman' => $request->alamat_pengambilan,
                'status_pengiriman' => 'Menunggu Pengambilan',
                'catatan' => $request->catatan
            ]
        );
        
        return redirect()->back()->with('success', 'Jadwal pengambilan berhasil dibuat.');
    }

    public function generateSalesNote($transactionId)
    {
        $transaction = Transaksi::with([
            'pembeli.user', 
            'detailTransaksi.barang',
            'pengiriman'
        ])->findOrFail($transactionId);
        
        return view('dashboard.warehouse.sales-note', compact('transaction'));
    }

    public function confirmItemReceived(Request $request, $transactionId)
    {
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Update pengiriman status
        if ($transaction->pengiriman) {
            $transaction->pengiriman->update([
                'status_pengiriman' => 'Selesai',
                'tanggal_terima' => now()
            ]);
        }
        
        // Update transaction status
        $transaction->update([
            'status_transaksi' => 'Selesai'
        ]);
        
        // Update item status to sold
        foreach ($transaction->detailTransaksi as $detail) {
            $detail->barang->update([
                'status_barang' => 'Terjual'
            ]);
        }
        
        return redirect()->back()->with('success', 'Konfirmasi penerimaan barang berhasil.');
    }

    public function updateTransactionStatus(Request $request, $transactionId)
    {
        $request->validate([
            'status_transaksi' => 'required|in:Menunggu Pembayaran,Lunas,Dibatalkan,Selesai',
            'status_barang' => 'nullable|in:Aktif,Tidak Aktif,Terjual,Dikembalikan'
        ]);
        
        $transaction = Transaksi::findOrFail($transactionId);
        
        // Update transaction status
        $transaction->update([
            'status_transaksi' => $request->status_transaksi
        ]);
        
        // Update item status if provided
        if ($request->status_barang) {
            foreach ($transaction->detailTransaksi as $detail) {
                $detail->barang->update([
                    'status_barang' => $request->status_barang
                ]);
            }
        }
        
        // Auto-create donation if transaction is cancelled after 2 days
        if ($request->status_transaksi == 'Dibatalkan') {
            $daysSinceOrder = $transaction->created_at->diffInDays(now());
            if ($daysSinceOrder >= 2) {
                // Logic for automatic donation
                foreach ($transaction->detailTransaksi as $detail) {
                    $detail->barang->update([
                        'status_barang' => 'Untuk Donasi'
                    ]);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }
}