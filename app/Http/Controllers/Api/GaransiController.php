<?php
namespace App\Http\Controllers;

use App\UseCases\Garansi\GaransiUseCase;
use App\DTOs\Garansi\CreateGaransiRequest;
use App\DTOs\Garansi\UpdateGaransiRequest;
use Illuminate\Http\Request;

class GaransiController extends Controller
{
    protected GaransiUseCase $garansiUseCase;

    public function __construct(GaransiUseCase $garansiUseCase)
    {
        $this->garansiUseCase = $garansiUseCase;
    }

    /**
     * Menampilkan daftar semua garansi.
     */
    public function index()
    {
        $garansi = $this->garansiUseCase->getAll();
        return response()->json($garansi);
    }

    /**
     * Menampilkan detail garansi berdasarkan ID.
     */
    public function show($id)
    {
        $garansi = $this->garansiUseCase->find($id);

        if (!$garansi) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json($garansi);
    }

    /**
     * Membuat data garansi baru.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'required|string',
            'tanggal_aktif' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_aktif',
        ]);

        // Membuat request DTO dan mengirimkannya ke use case
        $createRequest = new CreateGaransiRequest($validated);
        $garansi = $this->garansiUseCase->create($createRequest);

        return response()->json($garansi, 201);  // Mengembalikan data garansi yang baru dibuat
    }

    /**
     * Memperbarui data garansi berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'required|string',
            'tanggal_aktif' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_aktif',
        ]);

        // Membuat request DTO dan mengirimkannya ke use case
        $updateRequest = new UpdateGaransiRequest($validated);
        $garansi = $this->garansiUseCase->update($id, $updateRequest);

        if (!$garansi) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json($garansi);
    }

    /**
     * Menghapus data garansi berdasarkan ID.
     */
    public function destroy($id)
    {
        $deleted = $this->garansiUseCase->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'Garansi tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Garansi berhasil dihapus']);
    }
}
