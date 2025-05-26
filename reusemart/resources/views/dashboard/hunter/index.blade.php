@extends('layouts.dashboard')

@section('title', 'Dashboard Hunter')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Hunter</h1>
        <a href="{{ route('dashboard.hunter.riwayat-penjemputan') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-history fa-sm text-white-50"></i> Lihat Riwayat Penjemputan
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Komisi Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Komisi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Barang Dijemput Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Barang Dijemput</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBarangDijemput }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Komisi Bulan Ini Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Komisi Bulan Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($komisiPerBulan->where('bulan', date('F'))->first()['total'] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Penjemputan Pending Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Penjemputan Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $riwayatPenjemputan->where('status', 'Menunggu Penjemputan')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Komisi Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Komisi Bulanan ({{ date('Y') }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="komisiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kategori Barang Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kategori Barang Dijemput</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($barangPerKategori as $index => $kategori)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ 'hsl(' . (($index * 30) % 360) . ', 70%, 50%)' }}"></i> {{ $kategori->nama_kategori }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Komisi Terbaru -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komisi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Penitip</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($komisiTerbaru as $komisi)
                                <tr>
                                    <td>{{ $komisi->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $komisi->barang->nama_barang }}</td>
                                    <td>{{ $komisi->penitip->user->name }}</td>
                                    <td>Rp {{ number_format($komisi->nominal_komisi, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data komisi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('dashboard.hunter.komisi') }}" class="btn btn-primary btn-sm">Lihat Semua Komisi</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Penjemputan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Penjemputan Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Penitip</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPenjemputan as $penjemputan)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($penjemputan->tanggal_penjemputan)->format('d/m/Y') }}</td>
                                    <td>{{ $penjemputan->barang->nama_barang }}</td>
                                    <td>{{ $penjemputan->penitip->user->name }}</td>
                                    <td>
                                        @if($penjemputan->status == 'Menunggu Penjemputan')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($penjemputan->status == 'Dalam Proses')
                                            <span class="badge badge-info">Proses</span>
                                        @elseif($penjemputan->status == 'Selesai')
                                            <span class="badge badge-success">Selesai</span>
                                        @elseif($penjemputan->status == 'Dibatalkan')
                                            <span class="badge badge-danger">Batal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data penjemputan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('dashboard.hunter.riwayat-penjemputan') }}" class="btn btn-primary btn-sm">Lihat Semua Penjemputan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Komisi Chart
    var ctx = document.getElementById("komisiChart");
    var komisiChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($komisiPerBulan->pluck('bulan')) !!},
            datasets: [{
                label: "Komisi",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($komisiPerBulan->pluck('total')) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return 'Rp' + number_format(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    // Kategori Chart
    var kategoriCtx = document.getElementById("kategoriChart");
    var kategoriChart = new Chart(kategoriCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($barangPerKategori->pluck('nama_kategori')) !!},
            datasets: [{
                data: {!! json_encode($barangPerKategori->pluck('jumlah')) !!},
                backgroundColor: [
                    @foreach($barangPerKategori as $index => $kategori)
                        'hsl({{ ($index * 30) % 360 }}, 70%, 50%)',
                    @endforeach
                ],
                hoverBackgroundColor: [
                    @foreach($barangPerKategori as $index => $kategori)
                        'hsl({{ ($index * 30) % 360 }}, 70%, 40%)',
                    @endforeach
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
</script>
@endsection
