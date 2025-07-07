@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('customer.catalog') }}">Katalog</a></li>
            <li class="breadcrumb-item">{{ $product->category->name }}</li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset('images/products/' . $product->image) }}" 
                     class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                     style="height: 400px;">
                    <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                </div>
            @endif
        </div>
        
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">{{ $product->category->name }}</p>
            
            <h2 class="text-primary">{{ $product->getFormattedPrice() }}</h2>
            <p class="text-muted">per {{ $product->unit }}</p>
            
            <div class="mb-3">
                <span class="badge bg-success">Stok: {{ $product->stock }} {{ $product->unit }}</span>
            </div>
            
            <div class="mb-4">
                <h5>Deskripsi Produk</h5>
                <p>{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
            </div>
            
            @if($product->stock > 0)
                <form method="POST" action="{{ route('customer.cart.add', $product) }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="quantity" class="form-control" 
                                   value="1" min="1" max="{{ $product->stock }}" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary btn-lg me-md-2">
                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        <a href="{{ route('customer.catalog') }}" class="btn btn-outline-secondary btn-lg">
                            Kembali
                        </a>
                    </div>
                </form>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Produk sedang habis stok
                </div>
            @endif
        </div>
    </div>
    
    @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h3>Produk Terkait</h3>
            <div class="row">
                @foreach($relatedProducts as $related)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            @if($related->image)
                                <img src="{{ asset('images/products/' . $related->image) }}" 
                                     class="card-img-top" style="height: 150px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 150px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="card-body">
                                <h6 class="card-title">{{ Str::limit($related->name, 30) }}</h6>
                                <p class="card-text text-primary">{{ $related->getFormattedPrice() }}</p>
                                <a href="{{ route('customer.product.show', $related) }}" 
                                   class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection