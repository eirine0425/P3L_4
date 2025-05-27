<?php

namespace App\Repositories\Interfaces;

interface TransaksiPenitipanRepositoryInterface
{
    public function getAllTransaksiPenitipan();
    public function getTransaksiPenitipanById(int $transaksiPenitipanId);
    public function createTransaksiPenitipan(array $data);
    public function updateTransaksiPenitipan(int $transaksiPenitipanId, array $data);
    public function deleteTransaksiPenitipan(int $transaksiPenitipanId);
    public function getExpiringConsignments(int $days = 7): array;
    public function getExpiredConsignments(): array;
    public function getConsignmentsByStatus(string $statusDurasi): array;
}
