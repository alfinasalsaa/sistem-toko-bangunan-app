<?php

namespace App\Services;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PDFReceiptService
{
    private PythonServiceClient $pythonClient;
    
    public function __construct(PythonServiceClient $pythonClient)
    {
        $this->pythonClient = $pythonClient;
    }
    
    /**
     * Generate and sign receipt for transaction
     */
    public function generateSignedReceipt(Transaction $transaction): string
    {
        try {
            // Generate PDF
            $pdfPath = $this->generatePDF($transaction);
            
            // Check if Python service is available
            if (!$this->pythonClient->isServiceAvailable()) {
                // Return unsigned PDF if Python service not available
                $unsignedPath = 'receipts/receipt_' . $transaction->id . '.pdf';
                Storage::disk('public')->put($unsignedPath, file_get_contents($pdfPath));
                
                $transaction->update([
                    'receipt_path' => 'storage/' . $unsignedPath,
                ]);
                
                unlink($pdfPath);
                return storage_path('app/public/' . $unsignedPath);
            }
            
            // Prepare metadata for signing
            $metadata = [
                'transaction_id' => $transaction->id,
                'customer_name' => $transaction->user->name,
                'transaction_date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'total_amount' => $transaction->total_amount,
            ];
            
            // Sign the document with Python service
            $signResult = $this->pythonClient->signDocument($pdfPath, $metadata);
            
            if (!$signResult['success']) {
                throw new \Exception('Failed to sign receipt: ' . $signResult['error']);
            }
            
            // Download signed document
            $signedContent = $this->pythonClient->downloadSignedDocument(
                basename($signResult['signed_file_path'])
            );
            
            if (!$signedContent) {
                throw new \Exception('Failed to download signed receipt');
            }
            
            // Save signed receipt
            $signedPath = 'receipts/signed_receipt_' . $transaction->id . '.pdf';
            Storage::disk('public')->put($signedPath, $signedContent);
            
            // Update transaction with receipt info
            $transaction->update([
                'receipt_path' => 'storage/' . $signedPath,
                'signature_hash' => $signResult['document_hash'],
            ]);
            
            // Clean up temporary file
            unlink($pdfPath);
            
            return storage_path('app/public/' . $signedPath);
            
        } catch (\Exception $e) {
            // Fallback: generate unsigned PDF
            return $this->generateFallbackPDF($transaction);
        }
    }
    
    /**
     * Generate PDF receipt
     */
    private function generatePDF(Transaction $transaction): string
    {
        $data = [
            'transaction' => $transaction->load(['user', 'items.product', 'approvedBy']),
            'generated_at' => now(),
        ];
        
        $pdf = Pdf::loadView('receipts.template', $data);
        
        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Save temporary PDF
        $tempPath = $tempDir . '/receipt_' . $transaction->id . '.pdf';
        $pdf->save($tempPath);
        
        return $tempPath;
    }
    
    /**
     * Generate fallback PDF when Python service unavailable
     */
    private function generateFallbackPDF(Transaction $transaction): string
    {
        $pdfPath = $this->generatePDF($transaction);
        
        $fallbackPath = 'receipts/receipt_' . $transaction->id . '.pdf';
        Storage::disk('public')->put($fallbackPath, file_get_contents($pdfPath));
        
        $transaction->update([
            'receipt_path' => 'storage/' . $fallbackPath,
        ]);
        
        unlink($pdfPath);
        return storage_path('app/public/' . $fallbackPath);
    }
    
    /**
     * Verify receipt authenticity
     */
    public function verifyReceipt(string $filePath): array
    {
        return $this->pythonClient->verifyDocument($filePath);
    }
}