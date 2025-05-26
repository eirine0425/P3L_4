@extends('layouts.app')

@section('title', 'Profil Pembeli')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-user me-2"></i>Profil Pembeli</h4>
        </div>
        <div class="card-body">
            <p>Halo, {{ Auth::user()->name }}!</p>
            
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Email:</strong> {{ Auth::user()->email }}</li>
                <li class="list-group-item"><strong>No. Telepon:</strong> {{ Auth::user()->phone_number ?? '-' }}</li>
                <li class="list-group-item"><strong>Tanggal Lahir:</strong> {{ Auth::user()->dob ?? '-' }}</li>
                <tr>
                            <th>Poin Loyalitas</th>
                            <td>{{ $buyer->poin_loyalitas ?? 0 }} poin</td>
                        </tr>
            </ul>

            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary"><i class="fas fa-home me-1"></i> Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection
