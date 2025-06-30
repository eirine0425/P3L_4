<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Penjualan (dibawa oleh kurir)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 100%;
            max-width: 400px;
            border: 1px solid #000;
            padding: 15px;
            box-sizing: border-box;
            margin: 0 auto;
        }
        .title {
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .company {
            margin-bottom: 15px;
            font-weight: bold;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            display: inline-block;
            width: 100px;
        }
        .customer {
            margin: 15px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .points {
            margin: 15px 0;
        }
        .signature {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .signature-line {
            border-bottom: 1px dotted #000;
            height: 30px;
            margin: 10px 0;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Cetak Nota
        </button>
    </div>

    <div class="receipt">
        <div class="title">Nota Penjualan (dibawa oleh kurir)</div>
        
        <div class="company">
            ReUse Mart<br>
            Jl. Green Eco Park No. 456 Yogyakarta
        </div>
        
        <div class="info-row">
            <span class="info-label">No Nota</span>
            <span>: 25.02.101</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal pesan</span>
            <span>: 15/2/2025 18:50</span>
        </div>
        <div class="info-row">
            <span class="info-label">Lunas pada</span>
            <span>: 15/2/2024 19:01</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal kirim</span>
            <span>: 16/2/2024</span>
        </div>
        
        <div class="customer">
            <strong>Pembeli</strong> : cath123@gmail.com / Catherine<br>
            Perumahan Griya Persada Blok A/10, 20<br>
            Caturtunggal, Depok, Sleman<br>
            <strong>Delivery:</strong> Kurir ReUseMart (Cahyono)
        </div>
        
        <div class="items">
            <div class="item">
                <span>Kompor tanam 3 tungku</span>
                <span>2.000.000</span>
            </div>
            <div class="item">
                <span>Hair Dryer Ion</span>
                <span>500.000</span>
            </div>
        </div>
        
        <div class="totals">
            <div class="total-row">
                <span>Total</span>
                <span>2.500.000</span>
            </div>
            <div class="total-row">
                <span>Ongkos Kirim</span>
                <span>0</span>
            </div>
            <div class="total-row">
                <span>Total</span>
                <span>2.500.000</span>
            </div>
            <div class="total-row">
                <span>Potongan 200 poin</span>
                <span>- 20.000</span>
            </div>
            <div class="total-row" style="font-weight: bold;">
                <span>Total</span>
                <span>2.480.000</span>
            </div>
        </div>
        
        <div class="points">
            <div>Poin dari pesanan ini: 297</div>
            <div>Total poin customer: 300</div>
            <br>
            <div>QC oleh: Farida (P18)</div>
        </div>
        
        <div class="signature">
            <div>Diterima oleh:</div>
            <div class="signature-line"></div>
            <div>Tanggal: .............................</div>
        </div>
    </div>

    <script>
        // Auto print option (uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
