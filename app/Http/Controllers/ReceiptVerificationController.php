<?php
// app/Http/Controllers/ReceiptVerificationController.php

namespace App\Http\Controllers;

use App\Services\PythonServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReceiptVerificationController extends Controller
{
    private PythonServiceClient $pythonClient;
    
    public function __construct(PythonServiceClient $pythonClient)
    {
        $this->pythonClient = $pythonClient;
    }

    /**
     * Show verification form
     */
    public function showVerificationForm()
    {
        return view('receipts.verify');
    }

    /**
     * Verify uploaded receipt
     */
    public function verifyReceipt(Request $request)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('receipt');
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);

            // Call Python service for verification
            $verificationResult = $this->pythonClient->verifyDocument($fullPath);

            // Clean up temp file
            Storage::delete($tempPath);

            return view('receipts.verification-result', [
                'result' => $verificationResult,
                'filename' => $file->getClientOriginalName(),
            ]);

        } catch (\Exception $e) {
            Log::error('Receipt verification error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memverifikasi kuitansi: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint for verification (for mobile apps, etc)
     */
    public function verifyReceiptApi(Request $request)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $file = $request->file('receipt');
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);

            $verificationResult = $this->pythonClient->verifyDocument($fullPath);

            Storage::delete($tempPath);

            return response()->json([
                'success' => true,
                'verification' => $verificationResult,
                'filename' => $file->getClientOriginalName(),
            ]);

        } catch (\Exception $e) {
            Log::error('API receipt verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify receipt by QR code data (for QR scanning)
     */
    public function verifyByQrCode(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);
            
            if (!$qrData || !isset($qrData['transaction_id']) || !isset($qrData['document_hash'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid QR code data',
                ], 400);
            }

            // Verify signature using stored data
            $verificationResult = [
                'success' => true,
                'verification' => [
                    'transaction_id' => $qrData['transaction_id'],
                    'timestamp' => $qrData['timestamp'] ?? 'Unknown',
                    'signature_valid' => $this->verifyQrSignature($qrData),
                    'qr_valid' => true,
                    'message' => 'QR Code verification completed',
                ]
            ];

            return response()->json($verificationResult);

        } catch (\Exception $e) {
            Log::error('QR verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get verification statistics (for admin)
     */
    public function getVerificationStats()
    {
        // This would typically get data from a verification_logs table
        // For now, we'll return mock data
        $stats = [
            'total_verifications' => 0, // You can implement logging to track this
            'successful_verifications' => 0,
            'failed_verifications' => 0,
            'recent_verifications' => [], // Recent verification attempts
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Verify signature from QR code data
     */
    private function verifyQrSignature($qrData)
    {
        try {
            // This would call Python service to verify the signature
            $signature = hex2bin($qrData['signature'] ?? '');
            $documentHash = $qrData['document_hash'] ?? '';
            
            // Call verification service
            $result = $this->pythonClient->verifySignatureOnly($documentHash, $signature);
            
            return $result['signature_valid'] ?? false;
            
        } catch (\Exception $e) {
            Log::error('QR signature verification error: ' . $e->getMessage());
            return false;
        }
    }
}