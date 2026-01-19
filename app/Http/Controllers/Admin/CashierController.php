<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CashierController extends Controller
{
    /**
     * Show the cashier interface
     */
    public function index()
    {
        return view('admin.layouts.app');
    }

    /**
     * Search products by name or barcode
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('barcode', 'like', '%' . $query . '%')
                    ->orWhere('sku', 'like', '%' . $query . '%');
            })
            ->where('stock', '>', 0)
            ->select('id', 'name', 'barcode', 'sku', 'price', 'stock')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get product details by ID
     */
    public function getProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Create and complete an order
     */
    public function completeOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            $discount = $validated['discount'] ?? 0;

            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $tax = 0; // You can add tax calculation logic here
            $total = $subtotal - $discount + $tax;
            $change = $validated['paid_amount'] - $total;

            // Validate payment
            if ($validated['paid_amount'] < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient payment amount'
                ], 400);
            }

            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => 'cash',
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $change,
                'status' => 'completed'
            ]);

            // Create order items and deduct inventory
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => Product::find($item['product_id'])->name,
                    'product_sku' => Product::find($item['product_id'])->sku,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'discount' => 0,
                    'subtotal' => $item['quantity'] * $item['price']
                ]);

                // Deduct from inventory
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);

                // Track inventory transaction
                InventoryTransaction::create([
                    'product_id' => $item['product_id'],
                    'transaction_type' => 'sale',
                    'quantity_change' => -$item['quantity'],
                    'quantity_after' => $product->stock,
                    'user_id' => Auth::id(),
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'notes' => 'Cash sale at POS'
                ]);

                // Update last sold date and average daily sales
                $product->update([
                    'last_sold_date' => now(),
                    'average_daily_sales' => ($product->average_daily_sales ?? 0) + $item['quantity']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order completed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $total,
                    'paid_amount' => $validated['paid_amount'],
                    'change' => $change
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order history
     */
    public function getOrderHistory($limit = 10)
    {
        $orders = Order::with(['items', 'customer'])
            ->where('user_id', Auth::id())
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $number = $lastOrder ? (int)explode('-', $lastOrder->order_number)[1] + 1 : 1;
        return 'ORD-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Void an order (before payment confirmation)
     */
    public function voidOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::find($request->order_id);

        // Only void orders that haven't been paid yet
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only void pending orders'
            ], 400);
        }

        $order->update(['status' => 'voided']);

        return response()->json([
            'success' => true,
            'message' => 'Order voided successfully'
        ]);
    }
}
