@extends('layouts.dashboard')

@section('title', 'Detail Penitip')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Penitip</h3>
                        <div class="btn-group">
                            <a href="{{ route('dashboard.admin.penitips.edit', $penitip->penitip_id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('dashboard.admin.penitips') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Data Akun -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Data Akun</h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Nama Lengkap</strong></td>
                                    <td>: {{ $penitip->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $penitip->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Telepon</strong></td>
                                    <td>: {{ $penitip->user->phone_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Lahir</strong></td>
                                    <td>: {{ $penitip->user->dob ? \Carbon\Carbon::parse($penitip->user->dob)->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status Akun</strong></td>
                                    <td>: 
                                        @if($penitip->user && $penitip->user->email_verified_at)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Data Penitip -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Data Penitip</h5>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>ID Penitip</strong></td>
                                    <td>: {{ $penitip->penitip_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Penitip</strong></td>
                                    <td>: {{ $penitip->nama }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. KTP</strong></td>
                                    <td>: {{ $penitip->no_ktp }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Registrasi</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($penitip->tanggal_registrasi)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Badge</strong></td>
                                    <td>: 
                                        @if($penitip->badge && $penitip->badge !== 'no')
                                            <span class="badge badge-{{ $penitip->badge === 'gold' ? 'warning' : ($penitip->badge === 'silver' ? 'secondary' : ($penitip->badge === 'bronze' ? 'info' : 'primary')) }}">
                                                {{ ucfirst($penitip->badge) }}
                                            </span>
                                        @else
                                            <span class="badge badge-light">No Badge</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Periode</strong></td>
                                    <td>: {{ $penitip->periode ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Point Donasi</strong></td>
                                    <td>: {{ number_format($penitip->point_donasi ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Saldo</strong></td>
                                    <td>: Rp {{ number_format($penitip->saldo ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="btn-group">
                        <a href="{{ route('dashboard.admin.penitips.edit', $penitip->penitip_id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Data
                        </a>
                        <a href="{{ route('dashboard.admin.penitips') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Kembali ke Daftar
                        </a>
                        <form action="{{ route('dashboard.admin.penitips.destroy', $penitip->penitip_id) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus penitip ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
