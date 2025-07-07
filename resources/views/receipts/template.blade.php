<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi - {{ $transaction->transaction_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .company-info {
            margin-top: 10px;
            color: #666;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
            text-transform: uppercase;
        }
        
        .transaction-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        
        .signature-left, .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-box {
            border: 1px solid #ddd;
            height: 80px;
            margin: 10px 20px;
        }
        
        .signature-label {
            margin-top: 10px;
            font-weight: bold;
        }
        
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
        }
        
        .digital-signature {
            position: fixed;
            bottom: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">TOKO BANGUNAN JAYA</div>
        <div class="company-info">
            Jl. Konstruksi No. 123, Jakarta<br>
            Telp: (021) 1234-5678 | Email: info@tokobangunan.com
        </div>
    </div>
    
    <div class="receipt-title">Kuitansi Penjualan</div>
    
    <div class="transaction-info">
        <div class="info-left">
            <div class="info-row">
                <span class="info-label">No. Transaksi:</span>
                {{ $transaction->transaction_code }}
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                {{ $transaction->created_at->format('d/m/Y H:i') }}
            </div>
            <div class="info-row">
                <span class="info-label">Metode Bayar:</span>
                {{ strtoupper($transaction->payment_method) }}
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                {{ strtoupper($transaction->status) }}
            </div>
        </div>
        <div class="info-right">
            <div class="info-row">
                <span class="info-label">Nama Customer:</span>
                {{ $transaction->user->name }}
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                {{ $transaction->user->email }}
            </div>
            <div class="info-row">
                <span class="info-label">Telepon:</span>
                {{ $transaction->user->phone }}
            </div>
            <div class="info-row">
                <span class="info-label">Alamat:</span>
                {{ $transaction->user->address }}
            </div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Produk</th>
                <th style="width: 15%;">Harga</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Unit</th>
                <th style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-center">
                        @if($item->product)
                            {{ $item->product->unit }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    
    @if($transaction->admin_notes)
        <div class="notes">
            <strong>Catatan:</strong><br>
            {{ $transaction->admin_notes }}
        </div>
    @endif
    
    <div class="footer">
        <div class="signature-left">
            <div>Customer</div>
            <div class="signature-box"></div>
            <div class="signature-label">{{ $transaction->user->name }}</div>
        </div>
        <div class="signature-right">
            <div>Admin</div>
            <div class="signature-box"></div>
            <div class="signature-label">
                @if($transaction->approvedBy)
                    {{ $transaction->approvedBy->name }}
                @else
                    _________________
                @endif
                <div class="digital-signature">
                    <div style="font-weight: bold;">Dokumen Tersertifikasi Digital</div>
                    <div>Generated: {{ $generated_at->format('d/m/Y H:i:s') }}</div>
                    <div>Hash: {{ substr(md5($transaction->id . $transaction->created_at), 0, 8) }}</div>
                </div>
            </div>
        </div>
    </div>
    

</body>
</html>