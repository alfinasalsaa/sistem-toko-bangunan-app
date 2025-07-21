{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-pencil-square"></i> Edit Produk: {{ $product->name }}</h5>
                    <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $product->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3" placeholder="Deskripsi detail produk...">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price', $product->price) }}" 
                                               min="0" step="0.01" required
                                               placeholder="0">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Harga dalam Rupiah</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                                    <input type="number" name="stock" 
                                           class="form-control @error('stock') is-invalid @enderror" 
                                           value="{{ old('stock', $product->stock) }}" 
                                           min="0" required
                                           placeholder="0">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Jumlah stok tersedia</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                        <option value="">Pilih Unit</option>
                                        <option value="buah" {{ old('unit', $product->unit) == 'buah' ? 'selected' : '' }}>Buah</option>
                                        <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                        <option value="gram" {{ old('unit', $product->unit) == 'gram' ? 'selected' : '' }}>Gram</option>
                                        <option value="meter" {{ old('unit', $product->unit) == 'meter' ? 'selected' : '' }}>Meter</option>
                                        <option value="cm" {{ old('unit', $product->unit) == 'cm' ? 'selected' : '' }}>Centimeter</option>
                                        <option value="m2" {{ old('unit', $product->unit) == 'm2' ? 'selected' : '' }}>Meter Persegi (m²)</option>
                                        <option value="m3" {{ old('unit', $product->unit) == 'm3' ? 'selected' : '' }}>Meter Kubik (m³)</option>
                                        <option value="sak" {{ old('unit', $product->unit) == 'sak' ? 'selected' : '' }}>Sak</option>
                                        <option value="pail" {{ old('unit', $product->unit) == 'pail' ? 'selected' : '' }}>Pail</option>
                                        <option value="kaleng" {{ old('unit', $product->unit) == 'kaleng' ? 'selected' : '' }}>Kaleng</option>
                                        <option value="lembar" {{ old('unit', $product->unit) == 'lembar' ? 'selected' : '' }}>Lembar</option>
                                        <option value="batang" {{ old('unit', $product->unit) == 'batang' ? 'selected' : '' }}>Batang</option>
                                        <option value="roll" {{ old('unit', $product->unit) == 'roll' ? 'selected' : '' }}>Roll</option>
                                        <option value="dus" {{ old('unit', $product->unit) == 'dus' ? 'selected' : '' }}>Dus</option>
                                        <option value="set" {{ old('unit', $product->unit) == 'set' ? 'selected' : '' }}>Set</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gambar Produk Saat Ini</label>
                                    <div class="current-image-container">
                                        @if($product->image)
                                            <div class="current-image mb-2">
                                                <img src="{{ asset('images/products/' . $product->image) }}" 
                                                     class="img-thumbnail" style="max-width: 200px; max-height: 150px; object-fit: cover;"
                                                     alt="Current Product Image">
                                                <div class="mt-1">
                                                    <small class="text-muted">{{ $product->image }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="no-image mb-2">
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 200px; height: 150px; border: 2px dashed #ddd;">
                                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                                <small class="text-muted">Tidak ada gambar</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ganti Gambar Produk</label>
                                    <input type="file" name="image" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           accept="image/*">
                                    <small class="text-muted">
                                        Format: JPG, PNG, GIF. Maksimal 2MB<br>
                                        <em>Kosongkan jika tidak ingin mengganti gambar</em>
                                    </small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status Produk</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               id="is_active" value="1" 
                                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Produk Aktif
                                        </label>
                                    </div>
                                    <small class="text-muted">Produk tidak aktif tidak akan ditampilkan di katalog</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Info Terakhir Diupdate</label>
                                    <div class="bg-light p-2 rounded">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> 
                                            Dibuat: {{ $product->created_at->format('d/m/Y H:i') }}<br>
                                            <i class="bi bi-pencil"></i> 
                                            Diupdate: {{ $product->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Preview Card --}}
                        <div class="mb-4">
                            <label class="form-label">Preview Produk</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            @if($product->image)
                                                <img src="{{ asset('images/products/' . $product->image) }}" 
                                                     class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 100px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <p class="text-muted small mb-1">{{ $product->category->name }}</p>
                                            <p class="mb-1">{{ Str::limit($product->description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="h6 text-primary mb-0">{{ $product->getFormattedPrice() }}</span>
                                                <small class="text-muted">Stok: {{ $product->stock }} {{ $product->unit }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <a href="{{ route('admin.products') }}" class="btn btn-secondary me-2">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- Additional Actions --}}
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="bi bi-gear"></i> Aksi Tambahan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-grid">
                                <a href="{{ route('customer.product.show', $product) }}" 
                                   class="btn btn-outline-info" target="_blank">
                                    <i class="bi bi-eye"></i> Lihat di Katalog
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-warning" 
                                        onclick="duplicateProduct()">
                                    <i class="bi bi-files"></i> Duplikasi Produk
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-grid">
                                <form method="POST" action="{{ route('admin.products.delete', $product) }}" 
                                      onsubmit="return confirm('Yakin ingin menghapus produk ini? Aksi ini tidak dapat dibatalkan!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Hapus Produk
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Image preview when selecting new image
document.querySelector('input[name="image"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create preview
            const currentImageContainer = document.querySelector('.current-image-container');
            let previewContainer = document.querySelector('.image-preview');
            
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'image-preview mt-2';
                currentImageContainer.appendChild(previewContainer);
            }
            
            previewContainer.innerHTML = `
                <div class="border border-warning rounded p-2">
                    <small class="text-warning"><i class="bi bi-eye"></i> Preview Gambar Baru:</small>
                    <img src="${e.target.result}" class="img-thumbnail d-block mt-1" 
                         style="max-width: 200px; max-height: 150px; object-fit: cover;">
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});

// Duplicate product function
function duplicateProduct() {
    if (confirm('Duplikasi produk ini? Anda akan diarahkan ke halaman tambah produk dengan data yang sama.')) {
        // Create form to send product data to create page
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("admin.products.create") }}';
        
        // Add data as hidden inputs
        const data = {
            'duplicate': '{{ $product->id }}',
            'name': '{{ $product->name }} (Copy)',
            'description': '{{ addslashes($product->description) }}',
            'price': '{{ $product->price }}',
            'stock': '0', // Reset stock for duplicate
            'unit': '{{ $product->unit }}',
            'category_id': '{{ $product->category_id }}'
        };
        
        Object.keys(data).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Format price input
document.querySelector('input[name="price"]').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value) {
        // Remove non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, '');
        e.target.value = value;
    }
});

// Stock warning
document.querySelector('input[name="stock"]').addEventListener('input', function(e) {
    const stockValue = parseInt(e.target.value);
    const stockInput = e.target;
    
    // Remove existing warnings
    const existingWarning = stockInput.parentNode.querySelector('.stock-warning');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    if (stockValue <= 10 && stockValue > 0) {
        const warning = document.createElement('small');
        warning.className = 'stock-warning text-warning d-block';
        warning.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Stok rendah!';
        stockInput.parentNode.appendChild(warning);
    } else if (stockValue === 0) {
        const warning = document.createElement('small');
        warning.className = 'stock-warning text-danger d-block';
        warning.innerHTML = '<i class="bi bi-x-circle"></i> Stok habis - produk tidak akan muncul di katalog!';
        stockInput.parentNode.appendChild(warning);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="name"]').value;
    const price = document.querySelector('input[name="price"]').value;
    const stock = document.querySelector('input[name="stock"]').value;
    const unit = document.querySelector('select[name="unit"]').value;
    const category = document.querySelector('select[name="category_id"]').value;
    
    if (!name || !price || !stock || !unit || !category) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi (*)');
        return false;
    }
    
    if (parseFloat(price) <= 0) {
        e.preventDefault();
        alert('Harga harus lebih besar dari 0');
        return false;
    }
    
    if (parseInt(stock) < 0) {
        e.preventDefault();
        alert('Stok tidak boleh negatif');
        return false;
    }
});
</script>
@endpush
@endsection