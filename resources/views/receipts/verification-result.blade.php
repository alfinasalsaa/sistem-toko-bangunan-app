
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header {{ $result['success'] && ($result['verification']['overall_valid'] ?? false) ? 'bg-success' : 'bg-danger' }} text-white">
                    <h4 class="mb-0">
                        <i class="bi {{ $result['success'] && ($result['verification']['overall_valid'] ?? false) ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                        Hasil Verifikasi Kuitansi
                    </h4>
                </div>

                <div class="card-body">
                    @if($result['success'])
                        @php $verification = $result['verification']; @endphp
                        
                        <!-- Main Result Alert -->
                        <div class="alert {{ $verification['overall_valid'] ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show">
                            <div class="d-flex align-items-center">
                                <i class="bi {{ $verification['overall_valid'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-3" 
                                   style="font-size: 2rem;"></i>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $verification['message'] }}</h5>
                                    <p class="mb-0">File: <strong>{{ $filename }}</strong></p>
                                    <small>Diverifikasi pada: {{ now()->format('d/m/Y H:i:s') }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                        <div class="row">
                            <!-- Verification Status -->
                            <div class="col-md-6">
                                <div class="card border-{{ $verification['overall_valid'] ? 'success' : 'danger' }}">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Status Verifikasi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Integritas Dokumen:</span>
                                                <span class="badge {{ ($verification['document_integrity'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ($verification['document_integrity'] ?? false) ? 'Valid' : 'Tidak Valid' }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                {{ ($verification['document_integrity'] ?? false) ? 'Dokumen tidak mengalami perubahan' : 'Dokumen telah dimodifikasi' }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Tanda Tangan Digital:</span>
                                                <span class="badge {{ ($verification['signature_valid'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ($verification['signature_valid'] ?? false) ? 'Valid' : 'Tidak Valid' }}
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                {{ ($verification['signature_valid'] ?? false) ? 'Signature terverifikasi dengan kunci publik' : 'Signature tidak dapat diverifikasi' }}
                                            </small>
                                        </div>

                                        <div class="mb-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>Status Keseluruhan:</strong></span>
                                                <span class="badge {{ $verification['overall_valid'] ? 'bg-success' : 'bg-danger' }} fs-6">
                                                    {{ $verification['overall_valid'] ? 'DOKUMEN ASLI' : 'DOKUMEN TIDAK VALID' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Informasi Dokumen</h6>
                                    </div>
                                    <div class="card-body">
                                        @if(isset($verification['transaction_id']))
                                            <div class="mb-2">
                                                <strong>ID Transaksi:</strong><br>
                                                <code>{{ $verification['transaction_id'] }}</code>
                                            </div>
                                        @endif

                                        @if(isset($verification['timestamp']))
                                            <div class="mb-2">
                                                <strong>Tanggal Pembuatan:</strong><br>
                                                {{ $verification['timestamp'] }}
                                            </div>
                                        @endif

                                        @if(isset($verification['original_hash']))
                                            <div class="mb-2">
                                                <strong>Hash Dokumen:</strong><br>
                                                <small class="font-monospace text-muted">
                                                    {{ substr($verification['original_hash'], 0, 32) }}...
                                                </small>
                                            </div>
                                        @endif

                                        <div class="mb-0">
                                            <strong>Algoritma Keamanan:</strong><br>
                                            <small>
                                                <span class="badge bg-info">RSA-2048</span>
                                                <span class="badge bg-info">SHA-256</span>
                                                <span class="badge bg-info">QR-Code</span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details (Collapsible) -->
                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-link text-decoration-none p-0" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#technicalDetails">
                                        <i class="bi bi-gear"></i> Detail Teknis Verifikasi
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="collapse" id="technicalDetails">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Hash Comparison:</h6>
                                                <p><strong>Hash Asli:</strong></p>
                                                <code class="small">{{ $verification['original_hash'] ?? 'N/A' }}</code>
                                                
                                                <p class="mt-2"><strong>Hash Saat Ini:</strong></p>
                                                <code class="small">{{ $verification['current_hash'] ?? 'N/A' }}</code>
                                                
                                                <p class="mt-2">
                                                    <strong>Match:</strong> 
                                                    <span class="badge {{ ($verification['original_hash'] ?? '') === ($verification['current_hash'] ?? '') ? 'bg-success' : 'bg-danger' }}">
                                                        {{ ($verification['original_hash'] ?? '') === ($verification['current_hash'] ?? '') ? 'Yes' : 'No' }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Verification Process:</h6>
                                                <ul class="list-unstyled">
                                                    <li><i class="bi bi-check text-success"></i> PDF structure validated</li>
                                                    <li><i class="bi bi-check text-success"></i> QR code extracted</li>
                                                    <li><i class="bi bi-check text-success"></i> Digital signature decoded</li>
                                                    <li><i class="bi bi-check text-success"></i> Hash comparison performed</li>
                                                    <li><i class="bi bi-check text-success"></i> RSA signature verified</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Warning for Invalid Documents -->
                        @if(!$verification['overall_valid'])
                            <div class="alert alert-warning mt-4">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Peringatan Keamanan:</strong><br>
                                @if(!($verification['document_integrity'] ?? true))
                                    • Dokumen telah mengalami perubahan setelah ditandatangani digitally.<br>
                                @endif
                                @if(!($verification['signature_valid'] ?? true))
                                    • Tanda tangan digital tidak valid atau tidak dapat diverifikasi.<br>
                                @endif
                                <strong>Dokumen ini mungkin palsu atau telah dimanipulasi.</strong>
                            </div>
                        @endif

                    @else
                        <!-- Error Result -->
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <strong>Gagal Verifikasi:</strong> {{ $result['error'] }}
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6>Kemungkinan Penyebab:</h6>
                                <ul>
                                    <li>File PDF rusak atau corrupt</li>
                                    <li>File bukan kuitansi yang valid dari sistem kami</li>
                                    <li>QR code di dalam PDF tidak dapat dibaca</li>
                                    <li>Layanan verifikasi sedang bermasalah</li>
                                </ul>

                                <h6 class="mt-3">Solusi:</h6>
                                <ul>
                                    <li>Pastikan file PDF tidak rusak</li>
                                    <li>Download ulang kuitansi dari sistem</li>
                                    <li>Coba lagi beberapa saat</li>
                                    <li>Hubungi support jika masalah berlanjut</li>
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('receipts.verify') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Verifikasi Dokumen Lain
                        </a>
                        
                        <button class="btn btn-outline-primary ms-2" onclick="window.print()">
                            <i class="bi bi-printer"></i> Print Hasil
                        </button>
                        
                        <button class="btn btn-outline-success ms-2" onclick="shareResult()">
                            <i class="bi bi-share"></i> Share Hasil
                        </button>
                    </div>

                    <!-- Support Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <i class="bi bi-envelope display-6 text-primary"></i>
                                <p class="mb-0"><strong>Email Support:</strong></p>
                                <small>support@tokobangunan.com</small>
                            </div>
                            <div class="col-md-4">
                                <i class="bi bi-telephone display-6 text-primary"></i>
                                <p class="mb-0"><strong>Phone Support:</strong></p>
                                <small>(021) 1234-5678</small>
                            </div>
                            <div class="col-md-4">
                                <i class="bi bi-clock display-6 text-primary"></i>
                                <p class="mb-0"><strong>Jam Operasional:</strong></p>
                                <small>Senin-Jumat 08:00-17:00</small>
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
function shareResult() {
    if (navigator.share) {
        navigator.share({
            title: 'Hasil Verifikasi Kuitansi',
            text: 'Hasil verifikasi kuitansi {{ $filename }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link hasil verifikasi telah disalin ke clipboard');
        });
    }
}

// Print styles
window.addEventListener('beforeprint', function() {
    // Hide unnecessary elements when printing
    document.querySelectorAll('.btn, .alert .btn-close').forEach(el => {
        el.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    // Restore elements after printing
    document.querySelectorAll('.btn, .alert .btn-close').forEach(el => {
        el.style.display = '';
    });
});
</script>
@endpush
@endsection