<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\PDFReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    private PDFReceiptService $pdfService;
    
    public function __construct(PDFReceiptService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    
    /**
     * Generate and download receipt
     */
    public function generateReceipt(Transaction $transaction)
    {
        try {
            // Check authorization
            if (!auth()->user()->isAdmin() && $transaction->user_id !== auth()->id()) {
                abort(403);
            }
            
            // Check if transaction is approved
            if ($transaction->status !== 'approved') {
                return redirect()->back()->with('error', 'Kuitansi hanya dapat dibuat untuk transaksi yang sudah disetujui');
            }
            
            // Generate signed receipt
            $receiptPath = $this->pdfService->generateSignedReceipt($transaction);
            
            return response()->download($receiptPath, 'kuitansi_' . $transaction->transaction_code . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat kuitansi: ' . $e->getMessage());
        }
    }
    
    /**
     * Verify receipt authenticity
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
            
            $verificationResult = $this->pdfService->verifyReceipt($fullPath);
            
            // Clean up temp file
            Storage::delete($tempPath);
            
            return view('receipts.verification-result', [
                'result' => $verificationResult,
                'filename' => $file->getClientOriginalName(),
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memverifikasi kuitansi: ' . $e->getMessage());
        }
    }
    
    /**
     * Show verification form
     */
    public function showVerificationForm()
    {
        return view('receipts.verify');
    }
}