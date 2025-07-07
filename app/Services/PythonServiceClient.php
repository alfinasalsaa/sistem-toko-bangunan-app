<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonServiceClient
{
    private string $baseUrl;
    
    public function __construct()
    {
        $this->baseUrl = config('services.python.url', 'http://localhost:5000');
    }
    
    /**
     * Check if Python service is available
     */
    public function isServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Python service not available: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sign a PDF document
     */
    public function signDocument(string $filePath, array $metadata): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File not found: ' . $filePath);
            }
            
            $response = Http::timeout(60)
                ->attach('file', fopen($filePath, 'r'), basename($filePath))
                ->post($this->baseUrl . '/sign-document', $metadata);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('Failed to sign document: ' . $response->body());
            
        } catch (\Exception $e) {
            Log::error('Error signing document: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Verify a signed PDF document
     */
    public function verifyDocument(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('File not found: ' . $filePath);
            }

            $response = Http::timeout(30)
                ->attach('file', fopen($filePath, 'r'), basename($filePath))
                ->post($this->baseUrl . '/verify-document');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to verify document: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error verifying document: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Download signed document
     */
    public function downloadSignedDocument(string $filename): ?string
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . '/download/' . $filename);
            
            if ($response->successful()) {
                return $response->body();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error downloading signed document: ' . $e->getMessage());
            return null;
        }
    }

  
    public function verifySignatureOnly(string $documentHash, string $signature): array
    {
        try {
            $response = Http::timeout(30)
                ->post($this->baseUrl . '/verify-signature-only', [
                    'document_hash' => $documentHash,
                    'signature' => bin2hex($signature),
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to verify signature: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Error verifying signature: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get QR code data from PDF
     */
    public function extractQRData(string $filePath): ?array
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', fopen($filePath, 'r'), basename($filePath))
                ->post($this->baseUrl . '/extract-qr');

            if ($response->successful()) {
                $result = $response->json();
                return $result['qr_data'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error extracting QR data: ' . $e->getMessage());
            return null;
        }
    }
}