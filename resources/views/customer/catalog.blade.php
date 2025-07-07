{{-- resources/views/customer/catalog.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5>Filter Produk</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cari Produk</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nama produk..." value="{{ request('search') }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Urutkan</label>
                            <select name="sort_by" class="form-select">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Harga</option>
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                        <a href="{{ route('customer.catalog') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Katalog Produk</h2>
                <span class="text-muted">{{ $products->total() }} produk ditemukan</span>
            </div>
            
            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            @if($product->image)
                                <img src="{{ asset('images/products/' . $product->image) }}" 
                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted small">{{ $product->category->name }}</p>
                                <p class="card-text flex-grow-1">{{ Str::limit($product->description, 100) }}</p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="h5 text-primary mb-0">{{ $product->getFormattedPrice() }}</span>
                                        <small class="text-muted">per {{ $product->unit }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-success">Stok: {{ $product->stock }}</small>
                                        <a href="{{ route('customer.product.show', $product) }}" 
                                           class="btn btn-primary btn-sm">Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> Tidak ada produk yang ditemukan.
                        </div>
                    </div>
                @endforelse
            </div>
            
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection