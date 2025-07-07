<?php

namespace App\Providers;

use App\Services\PDFReceiptService;
use App\Services\PythonServiceClient;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind services untuk dependency injection
        $this->app->singleton(PythonServiceClient::class, function ($app) {
            return new PythonServiceClient();
        });
        
        $this->app->singleton(PDFReceiptService::class, function ($app) {
            return new PDFReceiptService($app->make(PythonServiceClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        // Create required directories
        $directories = [
            public_path('images/categories'),
            public_path('images/products'),
            public_path('uploads/payment_proofs'),
            storage_path('app/temp'),
            storage_path('app/public/receipts'),
        ];
        
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
}