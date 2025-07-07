@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Riwayat Transaksi</h2>
    
    @if($transactions->count() > 0)
        <div class="row">
            @foreach($transactions as $transaction)
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <h6>{{ $transaction->transaction_code }}</h6>
                                    <small class="text-muted">{{ $transaction->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge {{ $transaction->getStatusBadge() }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge bg-info">{{ strtoupper($transaction->payment_method) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>{{ $transaction->getFormattedTotal() }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('customer.transaction.show', $transaction) }}" 
                                       class="btn btn-sm btn-primary">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{ $transactions->links() }}
    @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-receipt display-1 text-muted"></i>
                        <h4 class="mt-3">Belum ada transaksi</h4>
                        <p class="text-muted">Mulai berbelanja untuk melihat riwayat transaksi</p>
                        <a href="{{ route('customer.catalog') }}" class="btn btn-primary">
                            Mulai Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection