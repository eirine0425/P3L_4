@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card bg-dark text-white border-0 rounded-3 overflow-hidden">
            <img src="https://via.placeholder.com/1200x400" class="card-img opacity-50" alt="About ReuseMart">
            <div class="card-img-overlay d-flex flex-column justify-content-center text-center">
                <h1 class="card-title display-4 fw-bold">Tentang ReuseMart</h1>
                <p class="card-text fs-5">Tempat jual beli barang bekas berkualitas. Bersama kita kurangi sampah dan berikan barang kesayangan Anda kesempatan kedua.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="mb-4"><i class="fas fa-building me-2"></i>Siapa Kami</h2>
                <p>ReuseMart adalah platform jual beli barang bekas berkualitas yang didirikan pada tahun 2020. Kami hadir dengan misi untuk mengurangi sampah dan memberikan barang-barang bekas kesempatan kedua untuk digunakan kembali.</p>
                <p>Kami percaya bahwa dengan memberikan kesempatan kedua pada barang-barang bekas, kita dapat mengurangi dampak negatif terhadap lingkungan dan memberikan manfaat ekonomi bagi masyarakat.</p>
                
                <h3 class="mt-4 mb-3">Visi Kami</h3>
                <p>Menjadi platform jual beli barang bekas terkemuka yang mendorong gaya hidup berkelanjutan dan ekonomi sirkular.</p>
                
                <h3 class="mt-4 mb-3">Misi Kami</h3>
                <ul>
                    <li>Menyediakan platform yang aman dan terpercaya untuk jual beli barang bekas berkualitas.</li>
                    <li>Mengurangi jumlah sampah dengan memperpanjang masa pakai barang.</li>
                    <li>Memberikan kesempatan bagi masyarakat untuk mendapatkan penghasilan tambahan melalui penitipan barang.</li>
                    <li>Mendukung organisasi sosial melalui program donasi barang.</li>
                    <li>Mengedukasi masyarakat tentang pentingnya gaya hidup berkelanjutan.</li>
                </ul>
                
                <h3 class="mt-4 mb-3">Nilai-Nilai Kami</h3>
                <div class="row row-cols-1 row-cols-md-3 g

Saya telah membuat struktur dasar untuk frontend aplikasi ReuseMart berdasarkan file-file backend yang telah disediakan. Berikut adalah komponen-komponen utama yang telah saya buat:

1. **Layout Utama**
   - Layout aplikasi umum (`app.blade.php`)
   - Layout dashboard (`dashboard.blade.php`)
   - Komponen navbar dan alerts

2. **Halaman Autentikasi**
   - Login
   - Register (dengan tab untuk Pembeli, Penitip, dan Organisasi)
   - Reset Password

3. **Halaman Utama**
   - Beranda dengan produk unggulan, kategori, dan informasi lainnya
   - Halaman daftar produk dengan filter dan pagination
   - Halaman detail produk dengan gambar, deskripsi, spesifikasi, ulasan, dan diskusi
   - Halaman cek garansi
   - Halaman tentang kami

Untuk melengkapi frontend ini, Anda perlu menambahkan:

1. Halaman dashboard untuk setiap peran (Owner, Admin, Pegawai Gudang, Pembeli, Penitip, CS)
2. Halaman manajemen data (CRUD) untuk setiap entitas
3. Halaman transaksi (keranjang, checkout, riwayat transaksi)
4. Halaman laporan

Semua halaman yang telah saya buat menggunakan Bootstrap untuk styling dan FontAwesome untuk ikon. Saya juga telah menambahkan JavaScript untuk interaktivitas pada beberapa halaman.
