{{-- resources/views/admin/transactions/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Manajemen Transaksi</h2>
    
    <div class="card">
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <strong>{{ $transaction->transaction_code }}</strong>
                                    </td>
                                    <td>
                                        {{ $transaction->user->name }}<br>
                                        <small class="text-muted">{{ $transaction->user->email }}</small>
                                    </td>
                                    <td>{{ $transaction->getFormattedTotal() }}</td>
                                    <td>
                                        <span class="badge {{ $transaction->payment_method == 'transfer' ? 'bg-info' : 'bg-warning' }}">
                                            {{ strtoupper($transaction->payment_method) }}
                                        </span>
                                        @if($transaction->payment_method == 'transfer' && $transaction->payment_proof)
                                            <br><small class="text-success">Bukti uploaded</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->getStatusBadge() }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                           class="btn btn-sm btn-primary">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $transactions->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-receipt display-1 text-muted"></i>
                    <h4 class="mt-3">Belum ada transaksi</h4>
                    <p class="text-muted">Transaksi akan muncul disini setelah customer membuat pesanan</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection