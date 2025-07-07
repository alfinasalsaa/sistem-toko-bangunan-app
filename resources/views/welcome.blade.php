<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-building"></i> Toko Bangunan
            </a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Sistem Manajemen Toko Bangunan</h1>
            <p class="lead mb-4">Platform lengkap untuk mengelola transaksi toko bangunan dengan sertifikat digital</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-person-plus"></i> Register
                </a>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="fw-bold">Fitur Unggulan</h2>
                    <p class="text-muted">Sistem terintegrasi untuk customer dan admin</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-shop display-4 text-primary mb-3"></i>
                            <h5>Katalog Produk</h5>
                            <p class="text-muted">Browse dan cari produk bangunan dengan mudah. Filter berdasarkan kategori dan harga.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-cart-check display-4 text-success mb-3"></i>
                            <h5>Shopping Cart</h5>
                            <p class="text-muted">Kelola pesanan dengan keranjang belanja yang mudah dan intuitif.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check display-4 text-warning mb-3"></i>
                            <h5>Digital Signature</h5>
                            <p class="text-muted">Kuitansi tersertifikasi digital dengan teknologi RSA-2048 + SHA-256.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-building"></i> Toko Bangunan</h5>
                    <p class="text-muted">Sistem manajemen transaksi terpercaya dengan teknologi digital signature.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">© 2024 Toko Bangunan. All rights reserved.</p>
                    <small class="text-muted">Powered by Laravel & Python</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>