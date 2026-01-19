<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /**
     * Get the customer for this order
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this order
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        $today = now()->format('Ymd');
        $latestOrder = self::where('order_number', 'like', 'ORD' . $today . '%')->latest('id')->first();
        
        if ($latestOrder) {
            $number = intval(substr($latestOrder->order_number, -3)) + 1;
        } else {
            $number = 1;
        }
        
        return 'ORD' . $today . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancel order and restore stock
     */
    public function cancel()
    {
        // Restore stock for all items
        foreach ($this->items as $item) {
            $product = $item->product;
            $product->stock += $item->quantity;
            $product->save();
        }

        // Restore customer balance if credit payment
        if ($this->payment_method === 'credit' && $this->customer) {
            $this->customer->updateBalance(-$this->total);
        }

        $this->status = 'cancelled';
        $this->save();
    }

    /**
     * Restore cancelled order and deduct stock
     */
    public function restore()
    {
        // Deduct stock for all items
        foreach ($this->items as $item) {
            $product = $item->product;
            $product->stock -= $item->quantity;
            $product->save();
        }

        // Apply customer balance if credit payment
        if ($this->payment_method === 'credit' && $this->customer) {
            $this->customer->updateBalance($this->total);
        }

        $this->status = 'completed';
        $this->save();
    }
}
