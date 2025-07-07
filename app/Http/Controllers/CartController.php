<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->carts()->with('product')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('customer.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->quantity;

        if (!$product->isInStock($quantity)) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi');
        }

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            if (!$product->isInStock($newQuantity)) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi');
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$cart->updateQuantity($request->quantity)) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi');
        }

        return redirect()->back()->with('success', 'Keranjang berhasil diupdate');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan');
    }
}