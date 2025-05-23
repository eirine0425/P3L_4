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
            background-color: var(--dark-color);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-brand {
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-decoration: none;
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-menu li {
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu li a {
            color: white;
            text-decoration: none;
            display: block;
        }
        
        .sidebar-menu li:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-menu li.active {
            background-color: var(--primary-color);
        }
        
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar-dashboard {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            margin-bottom: 20px;
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
        
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .stats-card h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .stats-card p {
            color: #666;
            margin-bottom: 0;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .content {
                margin-left: 0;
            }
            
            .content.active {
                margin-left: 250px;
            }
            
            #sidebarCollapse {
                display: block;
            }
        }
        
        #sidebarCollapse {
            display: none;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <a href="{{ url('/') }}" class="sidebar-brand">ReuseMart</a>
            </div>
            
            <ul class="sidebar-menu">
                @if(Auth::user()->role->nama_role == 'Owner')
                    <li class="{{ request()->is('dashboard/owner') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.owner') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/owner/reports') ? 'active' : '' }}">
                        <a href="{{ route('owner.reports.sales') }}"><i class="fas fa-chart-bar me-2"></i> Laporan</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employees') }}"><i class="fas fa-users me-2"></i> Pengguna</a>
                    </li>
                    <li>
                        <a href="{{ route('warehouse.items') }}"><i class="fas fa-box me-2"></i> Barang</a>
                    </li>
                    <li>
                        <a href="{{ route('consignor.transactions') }}"><i class="fas fa-shopping-cart me-2"></i> Transaksi</a>
                    </li>
                    <li>
                        <a href="{{ route('owner.donations') }}"><i class="fas fa-hand-holding-heart me-2"></i> Donasi</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'Admin')
                    <li class="{{ request()->is('dashboard/admin') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.admin') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/admin/users') ? 'active' : '' }}">
                        <a href="{{ route('admin.employees') }}"><i class="fas fa-users me-2"></i> Pengguna</a>
                    </li>
                    <li class="{{ request()->is('dashboard/admin/roles') ? 'active' : '' }}">
                        <a href="{{ route('admin.organizations') }}"><i class="fas fa-user-tag me-2"></i> Peran</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.employees') }}"><i class="fas fa-user-tie me-2"></i> Pegawai</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'Pegawai Gudang')
                    <li class="{{ request()->is('dashboard/warehouse') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.warehouse') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/warehouse/inventory') ? 'active' : '' }}">
                        <a href="{{ route('warehouse.items') }}"><i class="fas fa-boxes me-2"></i> Inventaris</a>
                    </li>
                    <li class="{{ request()->is('dashboard/warehouse/shipments') ? 'active' : '' }}">
                        <a href="{{ route('warehouse.shipments') }}"><i class="fas fa-truck me-2"></i> Pengiriman</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'CS')
                    <li class="{{ request()->is('dashboard/cs') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.cs') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/cs/customers') ? 'active' : '' }}">
                        <a href="{{ route('cs.consignors') }}"><i class="fas fa-users me-2"></i> Pelanggan</a>
                    </li>
                    <li class="{{ request()->is('dashboard/cs/discussions') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.cs.discussions') }}"><i class="fas fa-comments me-2"></i> Diskusi Produk</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'Penitip')
                    <li class="{{ request()->is('dashboard/consignor') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.consignor') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/consignor/items') ? 'active' : '' }}">
                        <a href="{{ route('consignor.items') }}"><i class="fas fa-box me-2"></i> Barang Saya</a>
                    </li>
                    <li class="{{ request()->is('dashboard/consignor/transactions') ? 'active' : '' }}">
                        <a href="{{ route('consignor.transactions') }}"><i class="fas fa-money-bill-wave me-2"></i> Transaksi</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'Pembeli')
                    <li class="{{ request()->is('dashboard/buyer') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.buyer') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/buyer/orders') ? 'active' : '' }}">
                        <a href="{{ route('buyer.transactions') }}"><i class="fas fa-shopping-bag me-2"></i> Pesanan Saya</a>
                    </li>
                    <li class="{{ request()->is('dashboard/buyer/profile') ? 'active' : '' }}">
                        <a href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> Profil</a>
                    </li>
                    <li class="{{ request()->is('cart') ? 'active' : '' }}">
                        <a href="{{ route('cart.index') }}"><i class="fas fa-shopping-cart me-2"></i> Keranjang</a>
                    </li>
                @elseif(Auth::user()->role->nama_role == 'Organisasi')
                    <li class="{{ request()->is('dashboard/organization') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.organization') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    </li>
                    <li class="{{ request()->is('dashboard/organization/donations') ? 'active' : '' }}">
                        <a href="{{ route('owner.donations') }}"><i class="fas fa-hand-holding-heart me-2"></i> Donasi</a>
                    </li>
                    <li class="{{ request()->is('dashboard/organization/requests') ? 'active' : '' }}">
                        <a href="{{ route('cs.payment.verifications') }}"><i class="fas fa-clipboard-list me-2"></i> Request Donasi</a>
                    </li>
                @endif
                
                <li>
                    <a href="{{ route('products.index') }}"><i class="fas fa-store me-2"></i> Katalog Produk</a>
                </li>
                <li>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        
        <!-- Content -->
        <div class="content">
            <nav class="navbar navbar-dashboard">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="d-flex">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
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
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('.content').toggleClass('active');
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
