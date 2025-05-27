@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $item->nama_barang }}</h1>
    <p><strong>Kategori:</strong> {{ $item->kategori->nama_kategori ?? '-' }}</p>
    <p><strong>Harga:</strong> Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
    <p><strong>Kondisi:</strong> {{ ucfirst(str_replace('_', ' ', $item->kondisi)) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($item->status) }}</p>
    <p><strong>Deskripsi:</strong> {{ $item->deskripsi }}</p>

   <h4>Foto Barang</h4>
<div class="row">
    @php
        $gambar = [
            'assets/jbl 1.jpg',
            'assets/jbl 2.jpg',
        ];
    @endphp

    @forelse ($gambar as $src)
        <div class="col-md-4 mb-3">
            <img src="{{ asset($src) }}" class="img-fluid rounded" alt="Foto Barang">
        </div>
    @empty
        <p class="text-muted">Belum ada foto barang.</p>
    @endforelse
</div>



    <a href="{{ route('dashboard.consignor.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
