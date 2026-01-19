<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Http\Requests\ProcessOrderRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        $status = $request->query('status');
        $customer = $request->query('customer');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        
        $query = Order::with(['customer', 'user', 'items']);
        
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }
        
        if ($status) {
            $query = $query->where('status', $status);
        }
        
        if ($customer) {
            $query = $query->where('customer_id', $customer);
        }
        
        if ($fromDate) {
            $query = $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query = $query->whereDate('created_at', '<=', $toDate);
        }
        
        $orders = $query->latest('created_at')->paginate(15);
        
        return response()->json($orders);
    }

    /**
     * Process a new order (POS).
     */
    public function store(ProcessOrderRequest $request)
    {
        $validated = $request->validated();
        
        return DB::transaction(function () use ($validated, $request) {
            // Calculate totals
            $subtotal = 0;
            $items = $validated['items'];
            
            // Validate stock and calculate subtotal
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                
                $itemTotal = ($item['quantity'] * $product->price) - ($item['discount'] ?? 0);
                $subtotal += $itemTotal;
            }
            
            $tax = $validated['tax'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $subtotal + $tax - $discount;
            $paidAmount = $validated['paid_amount'];
            $changeAmount = $paidAmount - $total;
            
            // Validate payment
            if ($validated['payment_method'] !== 'credit' && $paidAmount < $total) {
                throw new \Exception('Insufficient payment amount');
            }
            
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'paid_amount' => $paidAmount,
                'change_amount' => max(0, $changeAmount),
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create order items and deduct stock
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => ($item['quantity'] * $product->price) - ($item['discount'] ?? 0),
                ]);
                
                // Deduct stock
                $product->stock -= $item['quantity'];
                $product->save();
            }
            
            // Update customer balance if credit payment
            if ($validated['payment_method'] === 'credit' && $validated['customer_id']) {
                $customer = Customer::findOrFail($validated['customer_id']);
                $customer->updateBalance($total);
            }
            
            $order->load(['customer', 'user', 'items.product']);
            
            return response()->json([
                'message' => 'Order processed successfully',
                'data' => $order,
            ], 201);
        });
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'user', 'items.product']);
        
        return response()->json($order);
    }

    /**
     * Cancel an order (soft delete and restore stock).
     */
    public function cancel(Order $order)
    {
        if ($order->status === 'cancelled') {
            return response()->json([
                'message' => 'Order is already cancelled',
            ], 422);
        }
        
        return DB::transaction(function () use ($order) {
            $order->cancel();
            $order->delete();
            
            return response()->json([
                'message' => 'Order cancelled successfully',
            ]);
        });
    }

    /**
     * Restore a cancelled order.
     */
    public function restoreOrder($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        
        return DB::transaction(function () use ($order) {
            $order->restore();
            $order->restore();
            
            return response()->json([
                'message' => 'Order restored successfully',
                'data' => $order,
            ]);
        });
    }

    /**
     * Get daily sales.
     */
    public function dailySales(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        
        $sales = Order::where('status', 'completed')
            ->whereDate('created_at', $date)
            ->selectRaw('
                COUNT(*) as orders_count,
                SUM(subtotal) as subtotal,
                SUM(tax) as tax,
                SUM(discount) as discount,
                SUM(total) as total
            ')
            ->first();
        
        return response()->json($sales);
    }

    /**
     * Get sales by payment method.
     */
    public function salesByPaymentMethod(Request $request)
    {
        $fromDate = $request->query('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->query('to_date', now()->toDateString());
        
        $sales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->get();
        
        return response()->json($sales);
    }
}
