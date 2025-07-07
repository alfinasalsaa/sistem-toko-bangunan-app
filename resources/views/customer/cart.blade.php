{{-- resources/views/customer/cart.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Keranjang Belanja</h2>
    
    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-2">
                                    @if($item->product->image)
                                        <img src="{{ asset('images/products/' . $item->product->image) }}" 
                                             class="img-fluid rounded">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="height: 80px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <h6>{{ $item->product->name }}</h6>
                                    <p class="text-muted small mb-0">{{ $item->product->category->name }}</p>
                                    <p class="text-primary mb-0">{{ $item->product->getFormattedPrice() }} / {{ $item->product->unit }}</p>
                                </div>
                                <div class="col-md-3">
                                    <form method="POST" action="{{ route('customer.cart.update', $item) }}" 
                                          class="d-flex align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                               min="1" max="{{ $item->product->stock }}" 
                                               class="form-control form-control-sm me-2" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <p class="h6 mb-0">{{ $item->getFormattedSubtotal() }}</p>
                                </div>
                                <div class="col-md-1">
                                    <form method="POST" action="{{ route('customer.cart.remove', $item) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus item ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Ringkasan Belanja</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Item:</span>
                            <span>{{ $cartItems->sum('quantity') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        
                        <a href="{{ route('customer.checkout') }}" class="btn btn-primary w-100 mb-2">
                            Lanjut ke Pembayaran
                        </a>
                        
                        <form method="POST" action="{{ route('customer.cart.clear') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Kosongkan keranjang?')">
                                Kosongkan Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-cart-x display-1 text-muted"></i>
                        <h4 class="mt-3">Keranjang Kosong</h4>
                        <p class="text-muted">Belum ada produk di keranjang Anda</p>
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