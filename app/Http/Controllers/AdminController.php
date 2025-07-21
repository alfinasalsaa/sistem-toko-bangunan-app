<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\PDFReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'total_revenue' => Transaction::where('status', 'approved')->sum('total_amount'),
            'recent_transactions' => Transaction::with(['user', 'items.product'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('data'));
    }

    // Category Management
    public function categories()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $data['image'] = $imageName;
        }

        Category::create($data);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image && file_exists(public_path('images/categories/' . $category->image))) {
                unlink(public_path('images/categories/' . $category->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $data['image'] = $imageName;
        }

        $category->update($data);

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil diupdate');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk');
        }

        // Delete image
        if ($category->image && file_exists(public_path('images/categories/' . $category->image))) {
            unlink(public_path('images/categories/' . $category->image));
        }

        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dihapus');
    }

    // Product Management
    public function products()
    {
        $products = Product::with('category')->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'unit', 'category_id']);

        // if ($request->hasFile('image')) {
        //     $imageName = time() . '.' . $request->image->extension();
        //     $request->image->move(public_path('images/products'), $imageName);
        //     $data['image'] = $imageName;
        // }
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/images/products', $imageName); // simpan di storage/app/public/images/products
            $data['image'] = 'storage/images/products/' . $imageName; // path yang bisa digunakan di URL
        }

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function editProduct(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'stock', 'unit', 'category_id']);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::exists('public/images/products/' . $product->image)) {
                Storage::delete('public/images/products/' . $product->image);
            }

            // Simpan gambar baru
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/images/products', $imageName);

            // Simpan nama file ke database (path relatif ke 'public/storage')
            $data['image'] = $imageName;
        }

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil diupdate');
    }

    public function deleteProduct(Product $product)
    {
        // Delete image
        if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
            unlink(public_path('images/products/' . $product->image));
        }

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus');
    }

    // Transaction Management
    public function transactions()
    {
        $transactions = Transaction::with(['user', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function showTransaction(Transaction $transaction)
    {
        $transaction->load(['user', 'items.product', 'approvedBy']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function approveTransaction(Request $request, Transaction $transaction)
    {
        if (!$transaction->canBeApproved()) {
            return redirect()->back()->with('error', 'Transaksi tidak dapat disetujui');
        }

        $transaction->approve(auth()->id(), $request->admin_notes);

        // Generate PDF receipt
        try {
            $pdfService = app(PDFReceiptService::class);
            $pdfService->generateSignedReceipt($transaction);

            return redirect()->back()->with('success', 'Transaksi berhasil disetujui dan kuitansi telah dibuat');
        } catch (\Exception $e) {
            \Log::error('Failed to generate receipt: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Transaksi berhasil disetujui, namun gagal membuat kuitansi digital: ' . $e->getMessage());
        }
    }

    public function rejectTransaction(Request $request, Transaction $transaction)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $transaction->reject(auth()->id(), $request->admin_notes);

        return redirect()->back()->with('success', 'Transaksi berhasil ditolak');
    }

    private function generateReceipt(Transaction $transaction)
    {
        // Logic untuk generate PDF akan dibuat nanti
        // Ini akan memanggil Python service untuk digital signature
    }
}
