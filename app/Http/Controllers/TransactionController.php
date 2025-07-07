<?php


namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function checkout()
    {
        $cartItems = auth()->user()->carts()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.catalog')->with('error', 'Keranjang kosong');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('customer.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:transfer,cod',
            'payment_proof' => 'required_if:payment_method,transfer|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $cartItems = auth()->user()->carts()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.catalog')->with('error', 'Keranjang kosong');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if (!$item->product->isInStock($item->quantity)) {
                return redirect()->back()->with('error', 'Stok produk ' . $item->product->name . ' tidak mencukupi');
            }
        }

        DB::beginTransaction();

        try {
            // Create transaction
            $transaction = Transaction::create([
                'transaction_code' => Transaction::generateTransactionCode(),
                'user_id' => auth()->id(),
                'total_amount' => $cartItems->sum(function ($item) {
                    return $item->quantity * $item->price;
                }),
                'payment_method' => $request->payment_method,
                'status' => 'pending',
            ]);

            // Handle payment proof upload
            if ($request->hasFile('payment_proof')) {
                $fileName = 'proof_' . $transaction->id . '_' . time() . '.' . $request->payment_proof->extension();
                $request->payment_proof->move(public_path('uploads/payment_proofs'), $fileName);
                $transaction->payment_proof = $fileName;
                $transaction->save();
            }

            // Create transaction items
            foreach ($cartItems as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->price,
                ]);
            }

            // Clear cart
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            return redirect()->route('customer.transactions')->with('success', 'Transaksi berhasil dibuat. Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat transaksi');
        }
    }

    public function customerTransactions()
    {
        $transactions = auth()->user()->transactions()
            ->with(['items.product', 'approvedBy'])
            ->latest()
            ->paginate(10);

        return view('customer.transactions', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $transaction->load(['user', 'items.product', 'approvedBy']);
        return view('customer.transaction-detail', compact('transaction'));
    }

    public function downloadReceipt(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if (!$transaction->receipt_path || !file_exists(public_path($transaction->receipt_path))) {
            return redirect()->back()->with('error', 'Kuitansi tidak tersedia');
        }

        return response()->download(public_path($transaction->receipt_path));
    }
}