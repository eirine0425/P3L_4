<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'ReuseMart') - Tempat Jual Beli Barang Bekas Berkualitas</title>
    
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
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
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
        
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
        }
        
        .badge-top-seller {
            background-color: gold;
            color: black;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .product-card {
            transition: transform 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header>
        @include('partials.navbar')
    </header>
    
    <main class="py-4">
        <div class="container">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>
    
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>ReuseMart</h5>
                    <p>Tempat jual beli barang bekas berkualitas. Bersama kita kurangi sampah dan berikan barang kesayangan Anda kesempatan kedua.</p>
                </div>
                <div class="col-md-4">
                    <h5>Tautan</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-white">Beranda</a></li>
                        <li><a href="{{ url('/products') }}" class="text-white">Produk</a></li>
                        <li><a href="{{ url('/warranty') }}" class="text-white">Cek Garansi</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Kontak</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt me-2"></i> Jl. Contoh No. 123, Yogyakarta</p>
                        <p><i class="fas fa-phone me-2"></i> (0274) 123456</p>
                        <p><i class="fas fa-envelope me-2"></i> info@reusemart.com</p>
                    </address>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} ReuseMart. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
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
