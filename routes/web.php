<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReceiptVerificationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('customer.catalog');
        }
    }
    return view('welcome');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (auth()->user()->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/customer/catalog');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/register', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ]);

        auth()->login($user);

        return redirect('/customer/catalog');
    });
});

// Logout route
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // Redirect after login based on role
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('customer.catalog');
        }
    })->name('dashboard');

    // Customer Routes
    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::get('/catalog', [CustomerController::class, 'catalog'])->name('catalog');
        Route::get('/product/{product}', [CustomerController::class, 'showProduct'])->name('product.show');
        
        // Cart Routes
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
        
        // Transaction Routes
        Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [TransactionController::class, 'store'])->name('checkout.store');
        Route::get('/transactions', [TransactionController::class, 'customerTransactions'])->name('transactions');
        Route::get('/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
        Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'downloadReceipt'])->name('transaction.receipt');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Category Management
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
        Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
        Route::patch('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
        
        // Product Management
        Route::get('/products', [AdminController::class, 'products'])->name('products');
        Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
        Route::patch('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
        
        // Transaction Management
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/{transaction}', [AdminController::class, 'showTransaction'])->name('transactions.show');
        Route::patch('/transactions/{transaction}/approve', [AdminController::class, 'approveTransaction'])->name('transactions.approve');
        Route::patch('/transactions/{transaction}/reject', [AdminController::class, 'rejectTransaction'])->name('transactions.reject');
    });

        // Routes untuk verifikasi kuitansi (bisa diakses tanpa login)
    Route::get('/verify', [ReceiptVerificationController::class, 'showVerificationForm'])->name('receipts.verify');
    Route::post('/verify/upload', [ReceiptVerificationController::class, 'verifyReceipt'])->name('receipts.verify.upload');
    Route::post('/verify/qr', [ReceiptVerificationController::class, 'verifyByQrCode'])->name('receipts.verify.qr');

    // API routes untuk verifikasi (untuk mobile apps, dll)
    Route::prefix('api')->group(function () {
        Route::post('/verify-receipt', [ReceiptVerificationController::class, 'verifyReceiptApi'])->name('api.verify.receipt');
        Route::post('/verify-qr', [ReceiptVerificationController::class, 'verifyByQrCode'])->name('api.verify.qr');
        Route::get('/verification-stats', [ReceiptVerificationController::class, 'getVerificationStats'])->name('api.verification.stats');
    });

});