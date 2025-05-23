<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Komisi;
use App\Models\TransaksiPenitipan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardHunterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Mendapatkan total komisi
        $totalKomisi = DB::table('komisi')
            ->where('user_id', $user->id)
            ->sum('jumlah_komisi');
            
        // Mendapatkan jumlah barang yang dijemput
        $totalBarangDijemput = DB::table('transaksi_penitipan')
            ->where('hunter_id', $user->id)
            ->count();
            
        // Mendapatkan jumlah barang yang berhasil dijual
        $totalBarangTerjual = DB::table('barang')
            ->join('transaksi_penitipan', 'barang.transaksi_penitipan_id', '=', 'transaksi_penitipan.transaksi_penitipan_id')
            ->where('transaksi_penitipan.hunter_id', $user->id)
            ->where('barang.status_barang', 'Terjual')
            ->count();
            
        // Mendapatkan riwayat penjemputan terbaru
        $riwayatPenjemputan = DB::table('transaksi_penitipan')
            ->join('penitip', 'transaksi_penitipan.penitip_id', '=', 'penitip.penitip_id')
            ->join('users', 'penitip.user_id', '=', 'users.id')
            ->select(
                'transaksi_penitipan.*',
                'users.name as nama_penitip'
            )
            ->where('transaksi_penitipan.hunter_id', $user->id)
            ->orderBy('transaksi_penitipan.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Mendapatkan komisi terbaru
        $komisiTerbaru = DB::table('komisi')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('dashboard.hunter.index', compact(
            'totalKomisi',
            'totalBarangDijemput',
            'totalBarangTerjual',
            'riwayatPenjemputan',
            'komisiTerbaru'
        ));
    }
    
    public function komisi()
    {
        $user = Auth::user();
        
        $komisi = DB::table('komisi')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $totalKomisi = DB::table('komisi')
            ->where('user_id', $user->id)
            ->sum('jumlah_komisi');
            
        return view('dashboard.hunter.komisi', compact('komisi', 'totalKomisi'));
    }
    
    public function riwayatPenjemputan()
    {
        $user = Auth::user();
        
        $riwayatPenjemputan = DB::table('transaksi_penitipan')
            ->join('penitip', 'transaksi_penitipan.penitip_id', '=', 'penitip.penitip_id')
            ->join('users', 'penitip.user_id', '=', 'users.id')
            ->select(
                'transaksi_penitipan.*',
                'users.name as nama_penitip'
            )
            ->where('transaksi_penitipan.hunter_id', $user->id)
            ->orderBy('transaksi_penitipan.created_at', 'desc')
            ->paginate(10);
            
        return view('dashboard.hunter.riwayat-penjemputan', compact('riwayatPenjemputan'));
    }
    
    public function detailPenjemputan($id)
    {
        $user = Auth::user();
        
        $penjemputan = DB::table('transaksi_penitipan')
            ->join('penitip', 'transaksi_penitipan.penitip_id', '=', 'penitip.penitip_id')
            ->join('users', 'penitip.user_id', '=', 'users.id')
            ->leftJoin('alamat', 'transaksi_penitipan.alamat_id', '=', 'alamat.alamat_id')
            ->select(
                'transaksi_penitipan.*',
                'users.name as nama_penitip',
                'users.email as email_penitip',
                'users.phone as phone_penitip',
                'alamat.alamat_lengkap',
                'alamat.kota',
                'alamat.provinsi',
                'alamat.kode_pos'
            )
            ->where('transaksi_penitipan.transaksi_penitipan_id', $id)
            ->where('transaksi_penitipan.hunter_id', $user->id)
            ->first();
            
        if (!$penjemputan) {
            abort(404, 'Data penjemputan tidak ditemukan');
        }
        
        $barang = DB::table('barang')
            ->join('kategori_barang', 'barang.kategori_id', '=', 'kategori_barang.kategori_id')
            ->select(
                'barang.*',
                'kategori_barang.nama_kategori'
            )
            ->where('barang.transaksi_penitipan_id', $id)
            ->get();
            
        return view('dashboard.hunter.detail-penjemputan', compact('penjemputan', 'barang'));
    }
    
    public function updateStatusPenjemputan(Request $request, $id)
    {
        $user = Auth::user();
        
        $request->validate([
            'status_penjemputan' => 'required|in:Menunggu Konfirmasi,Dalam Proses,Selesai,Dibatalkan'
        ]);
        
        $penjemputan = DB::table('transaksi_penitipan')
            ->where('transaksi_penitipan_id', $id)
            ->where('hunter_id', $user->id)
            ->first();
            
        if (!$penjemputan) {
            abort(404, 'Data penjemputan tidak ditemukan');
        }
        
        DB::table('transaksi_penitipan')
            ->where('transaksi_penitipan_id', $id)
            ->update([
                'status_penjemputan' => $request->status_penjemputan,
                'updated_at' => now()
            ]);
            
        return redirect()->route('dashboard.hunter.detail-penjemputan', $id)
            ->with('success', 'Status penjemputan berhasil diperbarui');
    }
}
