@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Transaksi</h2>
        <a href="{{ route('admin.transactions') }}" class="btn btn-secondary">Kembali</a>
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
                                    <td><strong>Customer:</strong></td>
                                    <td>
                                        {{ $transaction->user->name }}<br>
                                        <small class="text-muted">{{ $transaction->user->email }}</small><br>
                                        <small class="text-muted">{{ $transaction->user->phone }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat:</strong></td>
                                    <td>{{ $transaction->user->address }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tanggal Pesanan:</strong></td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Metode Pembayaran:</strong></td>
                                    <td>
                                        <span class="badge {{ $transaction->payment_method == 'transfer' ? 'bg-info' : 'bg-warning' }}">
                                            {{ strtoupper($transaction->payment_method) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $transaction->getStatusBadge() }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($transaction->approved_at)
                                    <tr>
                                        <td><strong>Disetujui:</strong></td>
                                        <td>
                                            {{ $transaction->approved_at->format('d/m/Y H:i') }}<br>
                                            <small class="text-muted">oleh {{ $transaction->approvedBy->name }}</small>
                                        </td>
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
                                            <strong>{{ $item->product_name }}</strong><br>
                                            <small class="text-muted">
                                                @if($item->product)
                                                    {{ $item->product->category->name }}
                                                @endif
                                            </small>
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
                             class="img-fluid rounded" alt="Bukti Transfer"
                             data-bs-toggle="modal" data-bs-target="#proofModal"
                             style="cursor: pointer;">
                        <p class="mt-2 text-muted">Klik untuk memperbesar</p>
                    </div>
                </div>
            @endif
            
            @if($transaction->canBeApproved())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Aksi Admin</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.transactions.approve', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Catatan Admin (opsional)</label>
                                <textarea name="admin_notes" class="form-control" rows="3" 
                                          placeholder="Catatan untuk customer..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 mb-2"
                                    onclick="return confirm('Setujui transaksi ini?')">
                                <i class="bi bi-check-circle"></i> Setujui Transaksi
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-danger w-100" 
                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Tolak Transaksi
                        </button>
                    </div>
                </div>
            @endif
            
            @if($transaction->receipt_path)
                <div class="card">
                    <div class="card-header">
                        <h5>Kuitansi</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ asset($transaction->receipt_path) }}" 
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

<!-- Modal for Payment Proof -->
@if($transaction->payment_method == 'transfer' && $transaction->payment_proof)
    <div class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('uploads/payment_proofs/' . $transaction->payment_proof) }}" 
                         class="img-fluid" alt="Bukti Transfer">
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal for Reject Transaction -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.transactions.reject', $transaction) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="admin_notes" class="form-control" rows="4" required
                                  placeholder="Jelaskan alasan penolakan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection