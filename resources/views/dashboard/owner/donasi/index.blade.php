<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Donasi - ReuseMart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg,rgb(102, 234, 111) 0%,rgb(0, 104, 37) 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .filters {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-row {
            display: flex;
            gap: 20px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color:rgb(0, 238, 115);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background:rgb(70, 199, 104);
            color: white;
        }

        .btn-primary:hover {
            background:rgb(21, 109, 40);
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color:rgb(6, 132, 50);
            margin-bottom: 10px;
        }

        .summary-card .label {
            font-size: 1rem;
            color: #666;
            font-weight: 600;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chart-container h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 1.3rem;
        }

        .data-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .table-header h3 {
            color: #333;
            font-size: 1.3rem;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #555;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Laporan Donasi Barang</h1>
            <p>Pantau dan kelola donasi barang untuk organisasi sosial</p>
        </div>

        <div class="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" id="start_date" value="{{ date('Y-m-01') }}">
                </div>
                <div class="filter-group">
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" id="end_date" value="{{ date('Y-m-t') }}">
                </div>
                <div class="filter-group">
                    <label for="organisasi_id">Organisasi</label>
                    <select id="organisasi_id">
                        <option value="">Semua Organisasi</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button class="btn btn-primary" onclick="loadReport()">🔍 Filter Data</button>
                </div>
            </div>
        </div>

        <div id="loading" class="loading" style="display: none;">
            <p>⏳ Memuat data laporan...</p>
        </div>

        <div id="error" class="error" style="display: none;"></div>

        <div id="report-content" style="display: none;">
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="number" id="total-donasi">0</div>
                    <div class="label">Total Donasi</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="total-nilai">Rp 0</div>
                    <div class="label">Total Nilai</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="total-request">0</div>
                    <div class="label">Total Request</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="organisasi-terlibat">0</div>
                    <div class="label">Organisasi Terlibat</div>
                </div>
            </div>
            <div class="data-table">
                <div class="table-header">
                    <h3>📋 Detail Donasi </h3>
                    <div class="table-actions">
                        <a href="{{ route('dashboard.owner.donasi.print') }}" target="_blank" class="btn btn-info">🖨️ Print</a>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table id="donasi-table">
                        <thead>
                            <tr>
                                <th>ID Request</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Nilai</th>
                                <th>Penerima</th>
                                <th>Organisasi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="donasi-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let monthlyChart, categoryChart;
        
        // CSRF Token setup
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadOrganisasi();
            loadReport();
        });

        async function loadOrganisasi() {
            try {
                // Gunakan route web biasa dengan CSRF token
                const response = await fetch('/dashboard/owner/donasi/report-data?get_organisasi=1', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.status === 'success' && data.data.all_organisasi) {
                    const select = document.getElementById('organisasi_id');
                    select.innerHTML = '<option value="">Semua Organisasi</option>';
                    
                    data.data.all_organisasi.forEach(org => {
                        const option = document.createElement('option');
                        option.value = org.organisasi_id;
                        option.textContent = org.nama_organisasi;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading organisasi:', error);
            }
        }

        async function loadReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const organisasiId = document.getElementById('organisasi_id').value;

            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('report-content').style.display = 'none';

            try {
                const params = new URLSearchParams({
                    start_date: startDate,
                    end_date: endDate,
                    organisasi_id: organisasiId
                });

                // Gunakan route web biasa
                const response = await fetch(`/dashboard/owner/donasi/report-data?${params}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.status === 'success') {
                    displayReport(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load report');
                }
            } catch (error) {
                console.error('Error loading report:', error);
                document.getElementById('error').textContent = 'Error: ' + error.message;
                document.getElementById('error').style.display = 'block';
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }

        function displayReport(data) {
            // Update summary cards
            document.getElementById('total-donasi').textContent = data.summary.total_donasi;
            document.getElementById('total-nilai').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.summary.total_nilai_donasi);
            document.getElementById('total-request').textContent = data.summary.total_request;
            document.getElementById('organisasi-terlibat').textContent = data.summary.organisasi_terlibat;

            // Update table
            updateTable(data.donasi);

            document.getElementById('report-content').style.display = 'block';
        }

        function updateTable(donasi) {
            const tbody = document.getElementById('donasi-tbody');
            tbody.innerHTML = '';

            donasi.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.request_id || '-'}</td>
                    <td>${item.nama_barang || '-'}</td>
                    <td>${item.kategori_nama || item.nama_kategori || '-'}</td>
                    <td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga || 0)}</td>
                    <td>${item.nama_penerima || '-'}</td>
                    <td>${item.nama_organisasi || '-'}</td>
                    <td>${item.tanggal_donasi ? new Date(item.tanggal_donasi).toLocaleDateString('id-ID') : '-'}</td>
                    <td><span class="badge badge-success">Selesai</span></td>
                `;
                tbody.appendChild(row);
            });
        }

        async function exportData() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const organisasiId = document.getElementById('organisasi_id').value;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                organisasi_id: organisasiId
            });

            window.open(`/dashboard/owner/donasi/export-data?${params}`, '_blank');
        }
    </script>
</body>
</html>
