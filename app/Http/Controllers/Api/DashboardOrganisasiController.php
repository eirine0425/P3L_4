<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Donasi;
use App\Models\RequestDonasi;
use App\Models\Organisasi;
use App\Models\User;
use Carbon\Carbon;

class DashboardOrganisasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        // Hitung total donasi lewat relasi ke requestDonasi
        $totalDonations = Donasi::whereHas('requestDonasi', function ($query) use ($organization) {
            $query->where('organisasi_id', $organization->id);
        })->count();

        $pendingRequests = RequestDonasi::where('organisasi_id', $organization->id)
            ->where('status', 'Menunggu')
            ->count();

        $approvedRequests = RequestDonasi::where('organisasi_id', $organization->id)
            ->where('status', 'Disetujui')
            ->count();

        $recentDonations = Donasi::whereHas('requestDonasi', function ($query) use ($organization) {
            $query->where('organisasi_id', $organization->id);
        })
        ->with('barang')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        $recentRequests = RequestDonasi::where('organisasi_id', $organization->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.organization.index', compact(
            'organization',
            'totalDonations',
            'pendingRequests',
            'approvedRequests',
            'recentDonations',
            'recentRequests'
        ));
    }

    public function donations(Request $request)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $query = Donasi::whereHas('requestDonasi', function ($q) use ($organization) {
            $q->where('organisasi_id', $organization->id);
        })->with('barang');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        $donations = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.organization.donations.index', compact('donations', 'organization'));
    }

    public function showDonation($id)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        // Karena di DB id donasi biasanya primary key bernama id, saya ubah 'donasi_id' ke 'id'
        $donation = Donasi::where('id', $id)
            ->whereHas('requestDonasi', function ($q) use ($organization) {
                $q->where('organisasi_id', $organization->id);
            })
            ->with(['barang', 'barang.penitip.user'])
            ->first();

        if (!$donation) {
            return redirect()->route('dashboard.organization.donations')
                ->with('error', 'Donasi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        return view('dashboard.organization.donations.show', compact('donation', 'organization'));
    }

    public function requests(Request $request)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $query = RequestDonasi::where('organisasi_id', $organization->id);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.organization.requests.index', compact('requests', 'organization'));
    }

    public function createRequest()
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        return view('dashboard.organization.requests.create', compact('organization'));
    }

    public function storeRequest(Request $request)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah_kebutuhan' => 'required|integer|min:1',
            'tanggal_kebutuhan' => 'required|date|after:today',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $requestDonasi = new RequestDonasi();
        $requestDonasi->organisasi_id = $organization->id;
        $requestDonasi->judul = $validated['judul'];
        $requestDonasi->deskripsi = $validated['deskripsi'];
        $requestDonasi->jumlah_kebutuhan = $validated['jumlah_kebutuhan'];
        $requestDonasi->tanggal_kebutuhan = $validated['tanggal_kebutuhan'];
        $requestDonasi->status = 'Menunggu Persetujuan';

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/requests'), $imageName);
            $requestDonasi->gambar_path = 'images/requests/' . $imageName;
        }

        $requestDonasi->save();

        return redirect()->route('dashboard.organization.requests')
            ->with('success', 'Permintaan donasi berhasil dibuat dan sedang menunggu persetujuan.');
    }

    public function showRequest($id)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $requestDonasi = RequestDonasi::where('request_donasi_id', $id)
            ->where('organisasi_id', $organization->id)
            ->first();

        if (!$requestDonasi) {
            return redirect()->route('dashboard.organization.requests')
                ->with('error', 'Permintaan donasi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $relatedDonations = Donasi::where('request_id', $id)
            ->with('barang')
            ->get();

        return view('dashboard.organization.requests.show', compact('requestDonasi', 'organization', 'relatedDonations'));
    }

    public function editRequest($id)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $requestDonasi = RequestDonasi::where('request_donasi_id', $id)
            ->where('organisasi_id', $organization->id)
            ->first();

        if (!$requestDonasi) {
            return redirect()->route('dashboard.organization.requests')
                ->with('error', 'Permintaan donasi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($requestDonasi->status !== 'Menunggu Persetujuan') {
            return redirect()->route('dashboard.organization.requests.show', $id)
                ->with('error', 'Permintaan donasi yang sudah disetujui atau ditolak tidak dapat diedit.');
        }

        return view('dashboard.organization.requests.edit', compact('requestDonasi', 'organization'));
    }

    public function updateRequest(Request $request, $id)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $requestDonasi = RequestDonasi::where('request_donasi_id', $id)
            ->where('organisasi_id', $organization->id)
            ->first();

        if (!$requestDonasi) {
            return redirect()->route('dashboard.organization.requests')
                ->with('error', 'Permintaan donasi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($requestDonasi->status !== 'Menunggu Persetujuan') {
            return redirect()->route('dashboard.organization.requests.show', $id)
                ->with('error', 'Permintaan donasi yang sudah disetujui atau ditolak tidak dapat diedit.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jumlah_kebutuhan' => 'required|integer|min:1',
            'tanggal_kebutuhan' => 'required|date|after:today',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $requestDonasi->judul = $validated['judul'];
        $requestDonasi->deskripsi = $validated['deskripsi'];
        $requestDonasi->jumlah_kebutuhan = $validated['jumlah_kebutuhan'];
        $requestDonasi->tanggal_kebutuhan = $validated['tanggal_kebutuhan'];

        if ($request->hasFile('gambar')) {
            if ($requestDonasi->gambar_path && file_exists(public_path($requestDonasi->gambar_path))) {
                unlink(public_path($requestDonasi->gambar_path));
            }

            $image = $request->file('gambar');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/requests'), $imageName);
            $requestDonasi->gambar_path = 'images/requests/' . $imageName;
        }

        $requestDonasi->save();

        return redirect()->route('dashboard.organization.requests.show', $id)
            ->with('success', 'Permintaan donasi berhasil diperbarui.');
    }

    public function destroyRequest($id)
    {
        $user = Auth::user();
        $organization = Organisasi::where('user_id', $user->id)->first();

        if (!$organization) {
            return redirect()->route('dashboard')->with('error', 'Profil organisasi tidak ditemukan.');
        }

        $requestDonasi = RequestDonasi::where('request_donasi_id', $id)
            ->where('organisasi_id', $organization->id)
            ->first();

        if (!$requestDonasi) {
            return redirect()->route('dashboard.organization.requests')
                ->with('error', 'Permintaan donasi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($requestDonasi->status !== 'Menunggu Persetujuan') {
            return redirect()->route('dashboard.organization.requests.show', $id)
                ->with('error', 'Permintaan donasi yang sudah disetujui atau ditolak tidak dapat dihapus.');
        }

        if ($requestDonasi->gambar_path && file_exists(public_path($requestDonasi->gambar_path))) {
            unlink(public_path($requestDonasi->gambar_path));
        }

        $requestDonasi->delete();

        return redirect()->route('dashboard.organization.requests')
            ->with('success', 'Permintaan donasi berhasil dihapus.');
    }
}
