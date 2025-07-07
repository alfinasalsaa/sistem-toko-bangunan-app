@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-shield-check"></i> Verifikasi Kuitansi Digital
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Cara Verifikasi Dokumen:</strong><br>
                                1. Upload file kuitansi PDF yang ingin diverifikasi<br>
                                2. Atau scan QR code yang ada di kuitansi<br>
                                3. Sistem akan mengecek keaslian dan integritas dokumen
                            </div>

                            <!-- File Upload Form -->
                            <div class="mb-4">
                                <h5><i class="bi bi-upload"></i> Upload File Kuitansi</h5>
                                <form method="POST" action="{{ route('receipts.verify.upload') }}" enctype="multipart/form-data" id="uploadForm">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Pilih File Kuitansi (PDF)</label>
                                        <div class="input-group">
                                            <input type="file" name="receipt" class="form-control @error('receipt') is-invalid @enderror" 
                                                   accept=".pdf" required id="fileInput">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-search"></i> Verifikasi
                                            </button>
                                        </div>
                                        <small class="text-muted">Hanya file PDF yang diperbolehkan. Maksimal 10MB.</small>
                                        @error('receipt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </form>

                                <!-- Drag & Drop Area -->
                                <div class="border border-dashed border-primary rounded p-4 text-center mb-3" 
                                     id="dropZone" style="background-color: #f8f9ff;">
                                    <i class="bi bi-cloud-upload display-4 text-primary"></i>
                                    <p class="mt-2 mb-0">
                                        <strong>Drag & Drop file PDF disini</strong><br>
                                        <small class="text-muted">atau klik "Pilih File" di atas</small>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- QR Code Scanner -->
                            <div class="mb-4">
                                <h5><i class="bi bi-qr-code-scan"></i> Scan QR Code</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <button class="btn btn-success w-100 mb-2" onclick="startQRScanner()">
                                            <i class="bi bi-camera"></i> Buka Kamera
                                        </button>
                                        <div id="qr-reader" style="width: 100%; display: none;"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-light">
                                            <strong>Cara Scan:</strong><br>
                                            1. Klik "Buka Kamera"<br>
                                            2. Arahkan kamera ke QR code di kuitansi<br>
                                            3. Tunggu hasil scan otomatis
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual QR Input -->
                            <div class="mb-4">
                                <h6><i class="bi bi-keyboard"></i> Input Manual QR Data</h6>
                                <form method="POST" action="{{ route('receipts.verify.qr') }}" id="qrForm">
                                    @csrf
                                    <div class="input-group">
                                        <textarea name="qr_data" class="form-control" rows="3" 
                                                  placeholder="Paste QR code data JSON disini..." id="qrDataInput"></textarea>
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-check-circle"></i> Verifikasi QR
                                        </button>
                                    </div>
                                    <small class="text-muted">Copy-paste data QR code jika tidak bisa scan</small>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Security Info -->
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bi bi-shield-check"></i> Keamanan Verifikasi</h6>
                                </div>
                                <div class="card-body">
                                    <h6><i class="bi bi-check-circle text-success"></i> Yang Diperiksa:</h6>
                                    <ul class="list-unstyled">
                                        <li>✓ Keaslian tanda tangan digital</li>
                                        <li>✓ Integritas dokumen</li>
                                        <li>✓ Informasi transaksi</li>
                                        <li>✓ Timestamp pembuatan</li>
                                        <li>✓ Hash verification</li>
                                    </ul>

                                    <h6 class="mt-3"><i class="bi bi-shield text-primary"></i> Teknologi:</h6>
                                    <ul class="list-unstyled small">
                                        <li>• RSA-2048 Encryption</li>
                                        <li>• SHA-256 Hashing</li>
                                        <li>• QR Code Verification</li>
                                        <li>• Tamper Detection</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Recent Verifications (if any) -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Tips Verifikasi</h6>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <strong>Kuitansi Valid:</strong><br>
                                        • File PDF tidak rusak<br>
                                        • QR code terbaca jelas<br>
                                        • Tanda tangan digital utuh<br><br>
                                        
                                        <strong>Kuitansi Tidak Valid:</strong><br>
                                        • File telah dimodifikasi<br>
                                        • QR code rusak/palsu<br>
                                        • Signature tidak cocok<br><br>
                                        
                                        <strong>Support:</strong><br>
                                        Email: support@tokobangunan.com<br>
                                        Phone: (021) 1234-5678
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memverifikasi dokumen...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
let html5QrcodeScanner = null;

// File upload with drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadForm = document.getElementById('uploadForm');

    // Drag and drop functionality
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.backgroundColor = '#e3f2fd';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8f9ff';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8f9ff';
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            fileInput.files = files;
            // Auto submit form
            showLoading();
            uploadForm.submit();
        } else {
            alert('Harap upload file PDF yang valid.');
        }
    });

    dropZone.addEventListener('click', function() {
        fileInput.click();
    });

    // Auto submit on file select
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            showLoading();
            uploadForm.submit();
        }
    });
});

// QR Code Scanner
function startQRScanner() {
    const qrReaderElement = document.getElementById('qr-reader');
    qrReaderElement.style.display = 'block';

    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }

    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        },
        false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code matched = ${decodedText}`, decodedResult);
    
    // Stop scanning
    html5QrcodeScanner.clear();
    document.getElementById('qr-reader').style.display = 'none';

    // Fill QR data input
    document.getElementById('qrDataInput').value = decodedText;

    // Show success message
    showAlert('success', 'QR Code berhasil di-scan! Data telah diisi otomatis.');

    // Auto verify QR data
    verifyQRData(decodedText);
}

function onScanFailure(error) {
    // Handle scan failure - usually just ignore
    console.warn(`Code scan error = ${error}`);
}

// Verify QR Data
function verifyQRData(qrData) {
    showLoading();
    
    fetch('{{ route("receipts.verify.qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_data: qrData
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Show verification result
            showVerificationResult(data.verification);
        } else {
            showAlert('danger', 'Gagal memverifikasi QR Code: ' + data.error);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('danger', 'Terjadi kesalahan: ' + error.message);
    });
}

// Show verification result in modal
function showVerificationResult(verification) {
    const resultHtml = `
        <div class="modal fade" id="verificationModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header ${verification.signature_valid ? 'bg-success' : 'bg-danger'} text-white">
                        <h5 class="modal-title">
                            <i class="bi ${verification.signature_valid ? 'bi-check-circle' : 'bi-x-circle'}"></i>
                            Hasil Verifikasi QR Code
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert ${verification.signature_valid ? 'alert-success' : 'alert-danger'}">
                            <strong>${verification.message || (verification.signature_valid ? 'QR Code Valid' : 'QR Code Tidak Valid')}</strong>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Informasi Transaksi:</h6>
                                <p><strong>ID Transaksi:</strong> ${verification.transaction_id || 'N/A'}</p>
                                <p><strong>Timestamp:</strong> ${verification.timestamp || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Status Verifikasi:</h6>
                                <p>
                                    <i class="bi ${verification.signature_valid ? 'bi-check-circle text-success' : 'bi-x-circle text-danger'}"></i>
                                    Tanda Tangan: ${verification.signature_valid ? 'Valid' : 'Tidak Valid'}
                                </p>
                                <p>
                                    <i class="bi ${verification.qr_valid ? 'bi-check-circle text-success' : 'bi-x-circle text-danger'}"></i>
                                    QR Code: ${verification.qr_valid ? 'Valid' : 'Tidak Valid'}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" onclick="window.print()">Print Hasil</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('verificationModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add new modal
    document.body.insertAdjacentHTML('beforeend', resultHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
    modal.show();
}

// Utility functions
function showLoading() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) {
        modal.hide();
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of container
    const container = document.querySelector('.container');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@endsection
