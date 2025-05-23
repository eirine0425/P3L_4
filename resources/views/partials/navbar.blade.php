<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-recycle me-2"></i>ReuseMart
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="{{ url('/products') }}">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('warranty/check*') ? 'active' : '' }}" href="{{ url('/warranty/check') }}">Cek Garansi</a>
                </li>
            </ul>
            
            <div class="d-flex">
                <form class="d-flex me-2" action="{{ url('/products') }}" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Cari produk..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                </form>
                
                @guest
                <a href="{{ url('/login') }}" class="btn btn-outline-primary me-2">Masuk</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">Daftar</a>
                @else
                <div class="dropdown">
                    <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="{{ url('/dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ url('/dashboard/profil') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
                        
                        @if(auth()->user()->role->nama_role == 'Pembeli')
                        <li><a class="dropdown-item" href="{{ url('/dashboard/keranjang') }}"><i class="fas fa-shopping-cart me-2"></i>Keranjang</a></li>
                        <li><a class="dropdown-item" href="{{ url('/dashboard/transaksi') }}"><i class="fas fa-shopping-bag me-2"></i>Transaksi</a></li>
                        @endif
                        
                        @if(auth()->user()->role->nama_role == 'Penitip')
                        <li><a class="dropdown-item" href="{{ url('/dashboard/barang-saya') }}"><i class="fas fa-boxes me-2"></i>Barang Saya</a></li>
                        <li><a class="dropdown-item" href="{{ url('/dashboard/transaksi') }}"><i class="fas fa-exchange-alt me-2"></i>Transaksi</a></li>
                        @endif
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
                
                @if(auth()->user()->role->nama_role == 'Pembeli')
                <a href="{{ url('/dashboard/keranjang') }}" class="btn btn-outline-success ms-2 position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>
                @endif
                @endguest
            </div>
        </div>
    </div>
</nav>
