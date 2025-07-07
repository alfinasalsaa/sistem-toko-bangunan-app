@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Transaksi</h2>
        <a href="{{ route('customer.transactions') }}" class="btn btn-secondary">Kembali</a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Kode Transaksi:</strong></td>
                                    <td>{{ $transaction->transaction_code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $transaction->getStatusBadge() }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Metode Pembayaran:</strong></td>
                                    <td>{{ strtoupper($transaction->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td><strong>{{ $transaction->getFormattedTotal() }}</strong></td>
                                </tr>
                                @if($transaction->approved_at)
                                <tr>
                                    <td><strong>Disetujui:</strong></td>
                                    <td>{{ $transaction->approved_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($transaction->admin_notes)
                        <div class="alert alert-info">
                            <strong>Catatan Admin:</strong><br>
                            {{ $transaction->admin_notes }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaction->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product_name }}</strong>
                                        </td>
                                        <td>{{ $item->getFormattedPrice() }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->getFormattedSubtotal() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3">Total</th>
                                    <th>{{ $transaction->getFormattedTotal() }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            @if($transaction->payment_method == 'transfer' && $transaction->payment_proof)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Bukti Transfer</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('uploads/payment_proofs/' . $transaction->payment_proof) }}" 
                             class="img-fluid rounded" alt="Bukti Transfer">
                    </div>
                </div>
            @endif
            
            @if($transaction->receipt_path)
                <div class="card">
                    <div class="card-header">
                        <h5>Kuitansi</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('customer.transaction.receipt', $transaction) }}" 
                           class="btn btn-primary w-100" target="_blank">
                            <i class="bi bi-download"></i> Download Kuitansi
                        </a>
                        <small class="text-muted d-block mt-2">
                            Kuitansi tersertifikasi digital
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection