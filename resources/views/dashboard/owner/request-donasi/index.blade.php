<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Request Donasi - ReuseMart</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-color: #28a745;
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
            background: #28a745;
            color: white;
        }

        .btn-primary:hover {
            background: #218838;
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
            color: #28a745;
            margin-bottom: 10px;
        }

        .summary-card .label {
            font-size: 1rem;
            color: #666;
            font-weight: 600;
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
            justify-content: space-between;
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

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .request-description {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
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

            .request-description {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Laporan Request Donasi</h1>
            <p>Pantau permintaan donasi dari organisasi sosial</p>
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
                    <div class="number" id="total-request">0</div>
                    <div class="label">Total Request</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="pending-request">0</div>
                    <div class="label">Request Menunggu</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="fulfilled-request">0</div>
                    <div class="label">Request Disetujui</div>
                </div>
                <div class="summary-card">
                    <div class="number" id="organisasi-aktif">0</div>
                    <div class="label">Organisasi Aktif</div>
                </div>
            </div>

            <div class="data-table">
                <div class="table-header">
                    <h3>📋 Detail Request Donasi</h3>
                    <div class="table-actions">
                        <a href="{{ route('dashboard.owner.request-donasi.print') }}" target="_blank" class="btn btn-info">🖨️ Print</a>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table id="request-table">
                        <thead>
                            <tr>
                                <th>ID Organisasi</th>
                                <th>Nama Organisasi</th>
                                <th>Alamat</th>
                                <th>Request</th>
                            </tr>
                        </thead>
                        <tbody id="request-tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token setup
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadReport();
        });

        async function loadReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const statusFilter = '';

            document.getElementById('loading').style.display = 'block';
            document.getElementById('error').style.display = 'none';
            document.getElementById('report-content').style.display = 'none';

            try {
                const params = new URLSearchParams({
                    start_date: startDate,
                    end_date: endDate,
                    status: statusFilter
                });

                console.log('Loading report with params:', params.toString());

                const response = await fetch(`{{ route('dashboard.owner.request-donasi.report-data') }}?${params}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Response error:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                console.log('Response data:', data);

                if (data.status === 'success') {
                    displayReport(data.data);
                } else {
                    throw new Error(data.message || 'Failed to load report');
                }
            } catch (error) {
                console.error('Error loading report:', error);
                document.getElementById('error').innerHTML = `
                    <strong>Error loading data:</strong> ${error.message}<br>
                    <small>Please check the console for more details or contact support.</small>
                `;
                document.getElementById('error').style.display = 'block';
            } finally {
                document.getElementById('loading').style.display = 'none';
            }
        }

        function displayReport(data) {
            console.log('Displaying report data:', data);
            
            // Update summary cards with null checks
            document.getElementById('total-request').textContent = data.summary?.total_request || 0;
            document.getElementById('pending-request').textContent = data.summary?.pending_request || 0;
            document.getElementById('fulfilled-request').textContent = data.summary?.fulfilled_request || 0;
            document.getElementById('organisasi-aktif').textContent = data.summary?.organisasi_aktif || 0;

            // Update table
            updateTable(data.requests || []);

            document.getElementById('report-content').style.display = 'block';
        }

        function updateTable(requests) {
            const tbody = document.getElementById('request-tbody');
            tbody.innerHTML = '';

            console.log('Updating table with requests:', requests);

            if (!requests || requests.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                        <em>Tidak ada data request donasi untuk periode yang dipilih</em>
                    </td>
                `;
                tbody.appendChild(row);
                return;
            }

            requests.forEach((item, index) => {
                console.log(`Processing request ${index}:`, item);
                
                const statusBadge = getStatusBadge(item.status);
                const tanggalRequest = item.tanggal_request ? 
                    new Date(item.tanggal_request).toLocaleDateString('id-ID') : 
                    '-';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><strong>${item.organisasi_id || 'N/A'}</strong></td>
                    <td>${item.nama_organisasi || 'N/A'}</td>
                    <td>${item.alamat || 'Alamat tidak tersedia'}</td>
                    <td>
                        <div class="request-description" title="${item.request_description || ''}">
                            ${item.request_description || 'Tidak ada deskripsi'}
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function printReport() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate
            });

            window.open(`/dashboard/owner/request-donasi/print?${params}`, '_blank');
        }

        // Add refresh button functionality
        function refreshData() {
            loadReport();
        }
    </script>
</body>
</html>
