@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Dashboard Admin</h2>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $data['total_products'] }}</h4>
                            <p class="mb-0">Total Produk</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box-seam h1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $data['total_customers'] }}</h4>
                            <p class="mb-0">Total Customer</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people h1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $data['pending_transactions'] }}</h4>
                            <p class="mb-0">Transaksi Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock h1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5>Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</h5>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-cash h1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Transaksi Terbaru</h5>
                </div>
                <div class="card-body">
                    @if($data['recent_transactions']->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['recent_transactions'] as $transaction)
                                        <tr>
                                            <td>{{ $transaction->transaction_code }}</td>
                                            <td>{{ $transaction->user->name }}</td>
                                            <td>{{ $transaction->getFormattedTotal() }}</td>
                                            <td>
                                                <span class="badge {{ $transaction->getStatusBadge() }}">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.transactions.show', $transaction) }}" 
                                                   class="btn btn-sm btn-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada transaksi</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Produk
                        </a>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Kategori
                        </a>
                        <a href="{{ route('admin.transactions') }}" class="btn btn-outline-warning">
                            <i class="bi bi-clock"></i> Review Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection