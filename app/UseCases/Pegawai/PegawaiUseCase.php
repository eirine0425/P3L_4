<?php
namespace App\UseCases\Pegawai;

use App\Repositories\Interfaces\PegawaiRepositoryInterface;
use App\DTOs\Pegawai\CreatePegawaiRequest;
use App\DTOs\Pegawai\UpdatePegawaiRequest;
use App\DTOs\Pegawai\GetPegawaiPaginationRequest;

class PegawaiUseCase
{
    public function __construct(
        protected PegawaiRepositoryInterface $repository
    ) {}

    // Mendapatkan semua data pegawai
    public function getAll(GetPegawaiPaginationRequest $request): array
    {

        return $this->repository->getAll(
            perPage: $request->getPerPage(),
            page: $request->getPage(),
            search: $request->getSearch()
        );
    }
    // Menemukan pegawai berdasarkan ID
    public function find($id)
    {
        return $this->repository->find($id);
    }

    // Membuat pegawai baru
    public function create(CreatePegawaiRequest $request)
    {
        // Mengakses data menggunakan properti DTO
        $data = [
            'user_id' => $request->user_id,
            'nama_jabatan' => $request->nama_jabatan,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'nominal_komisi' => $request->nominal_komisi,
            'status_aktif' => $request->status_aktif,
            'nama' => $request->nama,
        ];

        return $this->repository->create($data);
    }

    // Mengupdate data pegawai berdasarkan ID
    public function update($id, UpdatePegawaiRequest $request)
    {
        $pegawai = $this->repository->find($id);

        if (!$pegawai) {
            return null;
        }

        // Mengakses data menggunakan properti DTO
        $data = $request->only([
            'nama_jabatan',
            'tanggal_bergabung',
            'nominal_komisi',
            'status_aktif',
            'nama',
        ]);
        



        return $this->repository->update($id, $data);
    }

    // Menghapus pegawai berdasarkan ID
    public function delete($id): bool
    {
        $pegawai = $this->repository->find($id);

        if (!$pegawai) {
            return false;
        }

        return $this->repository->delete($id);
    }
}