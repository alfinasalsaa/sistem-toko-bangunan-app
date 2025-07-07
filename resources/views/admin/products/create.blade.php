{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Tambah Produk Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price') }}" min="0" step="0.01" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" 
                                           value="{{ old('stock') }}" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                        <option value="">Pilih Unit</option>
                                        <option value="buah" {{ old('unit') == 'buah' ? 'selected' : '' }}>Buah</option>
                                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                        <option value="meter" {{ old('unit') == 'meter' ? 'selected' : '' }}>Meter</option>
                                        <option value="m2" {{ old('unit') == 'm2' ? 'selected' : '' }}>Meter Persegi</option>
                                        <option value="m3" {{ old('unit') == 'm3' ? 'selected' : '' }}>Meter Kubik</option>
                                        <option value="sak" {{ old('unit') == 'sak' ? 'selected' : '' }}>Sak</option>
                                        <option value="pail" {{ old('unit') == 'pail' ? 'selected' : '' }}>Pail</option>
                                        <option value="kaleng" {{ old('unit') == 'kaleng' ? 'selected' : '' }}>Kaleng</option>
                                        <option value="lembar" {{ old('unit') == 'lembar' ? 'selected' : '' }}>Lembar</option>
                                        <option value="batang" {{ old('unit') == 'batang' ? 'selected' : '' }}>Batang</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="text-end">
                            <a href="{{ route('admin.products') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection