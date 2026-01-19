<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'credit_limit',
        'balance',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Get all orders by this customer
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get available credit (credit_limit - balance)
     */
    public function availableCredit()
    {
        return $this->credit_limit - $this->balance;
    }

    /**
     * Check if customer can make purchase with credit
     */
    public function canPurchaseOnCredit($amount)
    {
        return $this->availableCredit() >= $amount;
    }

    /**
     * Update customer balance
     */
    public function updateBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Get total purchases
     */
    public function totalPurchases()
    {
        return $this->orders()->where('status', 'completed')->sum('total');
    }
}
