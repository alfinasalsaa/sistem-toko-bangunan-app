<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function catalog(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::with('category')->where('is_active', true)->where('stock', '>', 0);

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->sort_by ?? 'name';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(12);

        return view('customer.catalog', compact('products', 'categories'));
    }

        public function showProduct(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inStock()
            ->take(4)
            ->get();

        return view('customer.product-detail', compact('product', 'relatedProducts'));
    }
}