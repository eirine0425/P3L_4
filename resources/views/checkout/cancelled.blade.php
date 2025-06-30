@extends('layouts.app')

@section('title', 'Transaksi Dibatalkan')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <!-- Cancelled Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-4">
                    <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Transaksi Dibatalkan</h1>
                <p class="text-gray-600">Waktu pembayaran telah habis dan transaksi dibatalkan otomatis</p>
            </div>

            <!-- Transaction Info -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Detail Transaksi</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Transaksi:</span>
                        <span class="font-semibold">#{{ $transaction->transaksi_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Dibatalkan
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('products.index') }}" 
                   class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    🛍️ Belanja Lagi
                </a>
                
                <a href="{{ route('home') }}" 
                   class="block w-full bg-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-400 transition duration-200">
                    🏠 Kembali ke Beranda
                </a>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 rounded-lg p-4 text-left">
                <h4 class="font-semibold text-blue-800 mb-2">ℹ️ Informasi</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Barang telah dikembalikan ke stok dan tersedia untuk dibeli kembali</li>
                    <li>• Point yang digunakan telah dikembalikan ke akun Anda</li>
                    <li>• Anda dapat membuat pesanan baru kapan saja</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
