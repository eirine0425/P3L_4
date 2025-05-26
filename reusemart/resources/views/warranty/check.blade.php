@extends('layouts.app')

@section('title', 'Cek Garansi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Cek Status Garansi</h4>
            </div>
            <div class="card-body">
                <p class="mb-4">Masukkan nomor garansi atau nomor transaksi untuk memeriksa status garansi produk Anda.</p>
                
                <form action="{{ url('/warranty/check') }}" method="GET" class="mb-4">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" name="warranty_number" placeholder="Masukkan nomor garansi atau nomor transaksi" value="{{ request('warranty_number') }}" required>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-2"></i>Cek Garansi</button>
                    </div>
                </form>
                
                @if(request('warranty_number'))
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Hasil Pencarian</h5>
                        
                        @php
                        // Simulate warranty check result
                        $warrantyNumber = request('warranty_number');
                        $isValid = strlen($warrantyNumber) >= 5;
                        $isExpired = false;
                        
                        if ($isValid) {
                            $lastChar = substr($warrantyNumber, -1);
                            $isExpired = in_array($lastChar, ['0', '2', '4', '6', '8']);
                        }
                        @endphp
                        
                        @if($isValid)
                        <div class="alert {{ $isExpired ? 'alert-danger' : 'alert-success' }}">
                            <h5><i class="fas {{ $isExpired ? 'fa-times-circle' : 'fa-check-circle' }} me-2"></i>Status Garansi: {{ $isExpired ? 'Tidak Berlaku' : 'Berlaku' }}</h5>
                        </div>
                        
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="200">Nomor Garansi</th>
                                    <td>{{ $warrantyNumber }}</td>
                                </tr>
                                <tr>
                                    <th>Produk</th>
                                    <td>Laptop {{ $isExpired ? 'Asus' : 'Lenovo' }} {{ $isExpired ? 'VivoBook' : 'ThinkPad' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pembelian</th>
                                    <td>{{ date('d F Y', strtotime('-2 months')) }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mulai Garansi</th>
                                    <td>{{ date('d F Y', strtotime('-2 months')) }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Berakhir Garansi</th>
                                    <td>{{ date('d F Y', strtotime($isExpired ? '-1 day' : '+1 month')) }}</td>
                                </tr>
                                <tr>
                                    <th>Cakupan Garansi</th>
                                    <td>
                                        <ul class="mb-0">
                                            <li>Kerusakan hardware non-fisik</li>
                                            <li>Baterai dan adaptor</li>
                                            <li>Layar dan keyboard</li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <h6>Catatan:</h6>
                            <ul>
                                <li>Garansi tidak berlaku untuk kerusakan fisik akibat kelalaian pengguna.</li>
                                <li>Garansi tidak berlaku jika segel garansi rusak atau telah dibuka oleh pihak tidak berwenang.</li>
                                <li>Untuk klaim garansi, silakan hubungi customer service kami di nomor (0274) 123456.</li>
                            </ul>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Nomor garansi atau nomor transaksi tidak ditemukan. Silakan periksa kembali nomor yang Anda masukkan.
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Informasi Garansi</h5>
                        <p>ReuseMart memberikan garansi untuk setiap produk yang dijual. Berikut adalah informasi mengenai garansi kami:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Yang Termasuk Garansi</h6>
                                        <ul class="mb-0">
                                            <li>Kerusakan hardware non-fisik</li>
                                            <li>Baterai dan adaptor</li>
                                            <li>Layar dan keyboard</li>
                                            <li>Komponen internal</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-times-circle text-danger me-2"></i>Yang Tidak Termasuk Garansi</h6>
                                        <ul class="mb-0">
                                            <li>Kerusakan fisik akibat kelalaian</li>
                                            <li>Kerusakan akibat cairan</li>
                                            <li>Segel garansi rusak atau dibuka</li>
                                            <li>Software dan sistem operasi</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p>Untuk informasi lebih lanjut mengenai garansi, silakan hubungi customer service kami di nomor (0274) 123456 atau email ke cs@reusemart.com.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
