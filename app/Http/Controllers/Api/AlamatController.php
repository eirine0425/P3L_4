<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Alamat\AlamatUsecase;
use App\DTOs\Alamat\CreateAlamatRequest;
use App\DTOs\Alamat\UpdateALamatRequest;
use App\Models\Alamat;
use App\DTOs\Alamat\GetAlamatPaginationRequest;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function __construct(
        protected AlamatUsecase $alamatUsecase
    ) {}

    public function index(GetAlamatPaginationRequest $request)
    {
        return response()->json($this->alamatUsecase->getAll($request));
    }

    public function store(CreateAlamatRequest $request)
    {
        $user = $this->alamatUsecase->create($request);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $alamat = $this->alamatUsecase->find($id);
        return $alamat ? response()->json($alamat) : response()->json(['message' => 'alamat not found'], 404);
    }

    public function update(UpdateAlamatRequest $request, $id)
    {
        $alamat = $this->alamatUsecase->find($id);
        if (!$alamat) {
            return response()->json(['message' => 'Alamat not found'], 404);
        }

        $alamat->update($request->validated());

        return response()->json($alamat);
    }


    public function destroy($id)
    {
        return $this->alamatUsecase->delete($id)
            ? response()->json(['message' => 'Deleted successfully'])
            : response()->json(['message' => 'Not found'], 404);
    }

    public function createWeb()
    {
        return view('dashboard.buyer.alamat.create');
    }

    public function showWeb($id)
    {
        $user = Auth::user();
        $pembeli = $user->pembeli;
        
        $alamat = $pembeli->alamats()->findOrFail($id);
        
        return view('dashboard.buyer.alamat', compact('alamat'));
    }

    public function setDefault($id)
    {
        try {
            $user = Auth::user();
            $pembeli = $user->pembeli;
            
            $alamat = $pembeli->alamats()->findOrFail($id);
            
            // Set semua alamat pembeli menjadi tidak default
            $pembeli->alamats()->update(['status_default' => 'N']);
            
            // Set alamat yang dipilih menjadi default
            $alamat->update(['status_default' => 'Y']);
            
            return redirect()->route('buyer.alamat.index')->with('success', 'Alamat default berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah alamat default: ' . $e->getMessage());
        }
    }
}
