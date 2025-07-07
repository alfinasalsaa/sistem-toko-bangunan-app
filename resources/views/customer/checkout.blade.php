{{-- resources/views/customer/checkout.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Checkout</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                        <div class="row align-items-center border-bottom py-2">
                            <div class="col-md-6">
                                <h6>{{ $item->product->name }}</h6>
                                <p class="text-muted small mb-0">{{ $item->product->category->name }}</p>
                            </div>
                            <div class="col-md-2">
                                <span>{{ $item->quantity }} {{ $item->product->unit }}</span>
                            </div>
                            <div class="col-md-2">
                                <span>{{ $item->product->getFormattedPrice() }}</span>
                            </div>
                            <div class="col-md-2">
                                <span class="fw-bold">{{ $item->getFormattedSubtotal() }}</span>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="row mt-3">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong class="text-primary">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.checkout.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="transfer" value="transfer" required>
                                    <label class="form-check-label" for="transfer">
                                        <strong>Transfer Bank</strong><br>
                                        <small class="text-muted">Bank BCA: 1234567890 a.n. Toko Bangunan</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="cod" value="cod" required>
                                    <label class="form-check-label" for="cod">
                                        <strong>Cash on Delivery (COD)</strong><br>
                                        <small class="text-muted">Bayar saat barang diterima</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="transfer-proof" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Silakan transfer ke rekening di atas dan upload bukti transfer
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Bukti Transfer</label>
                                <input type="file" name="payment_proof" class="form-control" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB</small>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <a href="{{ route('customer.cart') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Buat Pesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <p><strong>{{ auth()->user()->name }}</strong></p>
                    <p class="mb-1">{{ auth()->user()->phone }}</p>
                    <p>{{ auth()->user()->address }}</p>
                    
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Untuk mengubah informasi, silakan update profil Anda
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferRadio = document.getElementById('transfer');
    const codRadio = document.getElementById('cod');
    const transferProof = document.getElementById('transfer-proof');
    const proofInput = document.querySelector('input[name="payment_proof"]');
    
    function toggleTransferProof() {
        if (transferRadio.checked) {
            transferProof.style.display = 'block';
            proofInput.required = true;
        } else {
            transferProof.style.display = 'none';
            proofInput.required = false;
        }
    }
    
    transferRadio.addEventListener('change', toggleTransferProof);
    codRadio.addEventListener('change', toggleTransferProof);
});
</script>
@endpush
@endsection