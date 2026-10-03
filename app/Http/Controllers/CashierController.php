<?php

namespace App\Http\Controllers;

use App\Models\Method;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashierController extends Controller
{
    public function index()
    {
        $methods = Method::orderBy('name', 'asc')->get();
        $items = Item::orderBy('name', 'asc')->get();

        return view('cashier', compact('methods', 'items'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.method_id' => 'nullable|integer',
            'cart.*.item_id' => 'nullable|integer',
            'cart.*.item_type' => 'nullable|string',
            'cart.*.name' => 'required|string',
            'cart.*.paper_type' => 'nullable|string',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,qris',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $cart = $request->input('cart');
        $paymentMethod = $request->input('payment_method');
        $paidAmount = (float) $request->input('paid_amount', 0);

        return DB::transaction(function () use ($cart, $paymentMethod, $paidAmount) {
            $grandTotal = 0;
            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $grandTotal += $subtotal;
            }

            if ($paymentMethod === 'qris') {
                $paidAmount = $grandTotal;
            }

            $changeAmount = max(0, $paidAmount - $grandTotal);

            // Generate transaction code
            $today = Carbon::now()->format('Ymd');
            $countToday = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
            $trxCode = 'TRX-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_code' => $trxCode,
                'user_id' => Auth::id(),
                'total_amount' => $grandTotal,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
            ]);

            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['qty'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_type' => $item['item_type'] ?? 'method',
                    'method_id' => $item['method_id'] ?? null,
                    'item_id' => $item['item_id'] ?? null,
                    'name' => $item['name'],
                    'paper_type' => $item['paper_type'] ?? '-',
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock if it's a physical item
                if (!empty($item['item_id'])) {
                    $physicalItem = Item::find($item['item_id']);
                    if ($physicalItem) {
                        $physicalItem->decrement('stock', $item['qty']);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction_code' => $transaction->transaction_code,
                'total_amount' => $grandTotal,
                'change_amount' => $changeAmount,
            ]);
        });
    }
}
