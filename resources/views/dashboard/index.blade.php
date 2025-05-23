@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h4>Selamat Datang, {{ Auth::user()->name }}!</h4>
                        <p>Anda telah berhasil login ke sistem ReuseMart.</p>
                    </div>
                    
                    <div class="text-center my-5">
                        <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                        <h3>{{ Auth::user()->name }}</h3>
                        <p class="text-muted">{{ Auth::user()->email }}</p>
                        <p><span class="badge bg-success">{{ Auth::user()->role->nama_role }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
