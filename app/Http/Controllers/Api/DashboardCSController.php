<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Barang;
use App\Models\DiskusiProduk;
use Illuminate\Http\Request;

class DashboardCSController extends Controller
{
    public function index()
    {
        $totalPenitip = Penitip::count();
        $totalPembeli = Pembeli::count();
        $verifikasiTertunda = Barang::where('status', 'pending')->count();
        $diskusiBelumDibalas = DiskusiProduk::whereNull('jawaban')->count();
        $diskusiTerbaru = DiskusiProduk::with(['pembeli.user', 'barang'])->latest('tanggal_diskusi')->take(5)->get();
        $barangUntukVerifikasi = Barang::where('status', 'pending')->with(['kategori', 'penitip.user'])->take(5)->get();

        return view('dashboard.cs.index', compact(
            'totalPenitip',
            'totalPembeli',
            'verifikasiTertunda',
            'diskusiBelumDibalas',
            'diskusiTerbaru',
            'barangUntukVerifikasi'
        ));
    }
}
