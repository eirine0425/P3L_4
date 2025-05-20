<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Dashboard') - ReuseMart</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2E7D32;
            --accent-color: #8BC34A;
            --light-color: #F1F8E9;
            --dark-color: #1B5E20;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .sidebar-sticky {
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: 1rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            font-weight: 500;
            color: #ced4da;
            padding: 0.75rem 1rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: var(--primary-color);
        }
        
        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            color: #adb5bd;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
            padding-top: 1rem;
            padding-bottom: 1rem;
            font-size: 1.25rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        
        .main-content {
            margin-left: 240px;
            padding: 2rem;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--light-color);
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .dropdown-menu {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item:hover {
            background-color: var(--light-color);
        }
        
        .badge-notification {
            position: absolute;
            top: 5px;
            right: 5px;
            padding: 3px 6px;
            border-radius: 50%;
            background-color: red;
            color: white;
            font-size: 0.7rem;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <div class="text-center mb-4 mt-3">
                        <h3 class="text-white">ReuseMart</h3>
                    </div>
                    
                    <div class="user-info text-center mb-4">
                        <img src="https://via.placeholder.com/80" alt="User Avatar" class="rounded-circle mb-2">
                        <h6 class="text-white">{{ auth()->user()->name ?? 'User' }}</h6>
                        <span class="badge bg-light text-dark">{{ auth()->user()->role->nama_role ?? 'Role' }}</span>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Owner', 'Admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/pegawai*') ? 'active' : '' }}" href="{{ url('/dashboard/pegawai') }}">
                                <i class="fas fa-users me-2"></i> Pegawai
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/jabatan*') ? 'active' : '' }}" href="{{ url('/dashboard/jabatan') }}">
                                <i class="fas fa-id-badge me-2"></i> Jabatan
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/organisasi*') ? 'active' : '' }}" href="{{ url('/dashboard/organisasi') }}">
                                <i class="fas fa-building me-2"></i> Organisasi
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/merchandise*') ? 'active' : '' }}" href="{{ url('/dashboard/merchandise') }}">
                                <i class="fas fa-gift me-2"></i> Merchandise
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['CS']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/penitip*') ? 'active' : '' }}" href="{{ url('/dashboard/penitip') }}">
                                <i class="fas fa-user-tag me-2"></i> Penitip
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/diskusi*') ? 'active' : '' }}" href="{{ url('/dashboard/diskusi') }}">
                                <i class="fas fa-comments me-2"></i> Diskusi Produk
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/verifikasi-pembayaran*') ? 'active' : '' }}" href="{{ url('/dashboard/verifikasi-pembayaran') }}">
                                <i class="fas fa-check-circle me-2"></i> Verifikasi Pembayaran
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/klaim-merchandise*') ? 'active' : '' }}" href="{{ url('/dashboard/klaim-merchandise') }}">
                                <i class="fas fa-box-open me-2"></i> Klaim Merchandise
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Pegawai Gudang']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/barang-titipan*') ? 'active' : '' }}" href="{{ url('/dashboard/barang-titipan') }}">
                                <i class="fas fa-box me-2"></i> Barang Titipan
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/pengiriman*') ? 'active' : '' }}" href="{{ url('/dashboard/pengiriman') }}">
                                <i class="fas fa-truck me-2"></i> Pengiriman
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/pengambilan*') ? 'active' : '' }}" href="{{ url('/dashboard/pengambilan') }}">
                                <i class="fas fa-hand-holding-box me-2"></i> Pengambilan Barang
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Owner']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/donasi*') ? 'active' : '' }}" href="{{ url('/dashboard/donasi') }}">
                                <i class="fas fa-hand-holding-heart me-2"></i> Donasi
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('dashboard/laporan*') ? 'active' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chart-bar me-2"></i> Laporan
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/penjualan') }}">Penjualan Bulanan</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/komisi') }}">Komisi Bulanan</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/stok') }}">Stok Gudang</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/kategori') }}">Penjualan per Kategori</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/masa-habis') }}">Barang Masa Habis</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/donasi') }}">Donasi Barang</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/request-donasi') }}">Request Donasi</a></li>
                                <li><a class="dropdown-item" href="{{ url('/dashboard/laporan/transaksi-penitip') }}">Transaksi Penitip</a></li>
                            </ul>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Pembeli', 'Penitip']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/profil*') ? 'active' : '' }}" href="{{ url('/dashboard/profil') }}">
                                <i class="fas fa-user me-2"></i> Profil
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/transaksi*') ? 'active' : '' }}" href="{{ url('/dashboard/transaksi') }}">
                                <i class="fas fa-shopping-cart me-2"></i> Transaksi
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Penitip']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/barang-saya*') ? 'active' : '' }}" href="{{ url('/dashboard/barang-saya') }}">
                                <i class="fas fa-boxes me-2"></i> Barang Saya
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->check() && in_array(auth()->user()->role->nama_role ?? '', ['Pembeli']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/alamat*') ? 'active' : '' }}" href="{{ url('/dashboard/alamat') }}">
                                <i class="fas fa-map-marker-alt me-2"></i> Alamat
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/keranjang*') ? 'active' : '' }}" href="{{ url('/dashboard/keranjang') }}">
                                <i class="fas fa-shopping-basket me-2"></i> Keranjang
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard/poin*') ? 'active' : '' }}" href="{{ url('/dashboard/poin') }}">
                                <i class="fas fa-star me-2"></i> Poin Reward
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main role="main" class="col-md-10 ml-sm-auto main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        @yield('page-actions')
                    </div>
                </div>
                
                @include('partials.alerts')
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    
    @stack('scripts')
</body>
</html>
