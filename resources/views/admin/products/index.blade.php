{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Produk</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset($product->image) }}" 
                                                 class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $product->name }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->getFormattedPrice() }}</td>
                                    <td>
                                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td>{{ $product->unit }}</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.delete', $product) }}" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $products->links() }}
            @else
                <div class="text-center py-4">
                    <i class="bi bi-box-seam-fill display-1 text-muted"></i>
                    <h4 class="mt-3">Belum ada produk</h4>
                    <p class="text-muted">Mulai dengan menambahkan produk pertama</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        Tambah Produk Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection