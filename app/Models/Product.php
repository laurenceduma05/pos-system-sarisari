<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Original fields
        'sku', 'name', 'slug', 'description', 'category_id', 'price', 'cost', 'stock', 
        'min_stock', 'barcode', 'image', 'is_active',
        
        // Product Information & Identification
        'brand', 'product_image', 'size', 'color', 'weight', 'location_bin', 
        'reorder_point', 'max_stock_level', 'tags',
        
        // Supplier/Vendor Management
        'supplier_name', 'supplier_contact', 'supplier_sku', 'lead_time_days', 
        'preferred_vendor', 'last_purchase_date', 'last_purchase_price',
        
        // Unit of Measure
        'purchase_unit', 'sales_unit', 'uom_conversion_factor', 'packaging_type',
        
        // Pricing & Financial
        'profit_margin_percent', 'tax_category', 'discount_eligible', 
        'wholesale_price', 'msrp',
        
        // Tracking & Analytics
        'expiration_date', 'batch_lot_number', 'serial_number', 'warranty_info', 
        'product_status', 'date_added', 'last_modified', 'last_sold_date', 
        'average_daily_sales', 'sales_velocity',
        
        // Multi-Location
        'quantity_by_location',
        
        // E-commerce
        'available_online', 'online_description', 'meta_tags',
        
        // Advanced Features
        'composite_items', 'alternative_products', 'seasonal', 'promotional_pricing', 
        'commission_rate', 'alerts_enabled', 'internal_notes',
        
        // Reporting & Compliance
        'product_group', 'cogs', 'inventory_valuation_method', 'hs_code', 
        'country_of_origin', 'compliance_certifications'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'reorder_point' => 'integer',
        'max_stock_level' => 'integer',
        'lead_time_days' => 'integer',
        'preferred_vendor' => 'boolean',
        'last_purchase_date' => 'datetime',
        'last_purchase_price' => 'decimal:2',
        'uom_conversion_factor' => 'decimal:2',
        'profit_margin_percent' => 'decimal:2',
        'discount_eligible' => 'boolean',
        'wholesale_price' => 'decimal:2',
        'msrp' => 'decimal:2',
        'expiration_date' => 'date',
        'average_daily_sales' => 'decimal:2',
        'quantity_by_location' => 'array',
        'available_online' => 'boolean',
        'composite_items' => 'array',
        'seasonal' => 'boolean',
        'promotional_pricing' => 'array',
        'commission_rate' => 'decimal:2',
        'alerts_enabled' => 'boolean',
        'cogs' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category of this product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get order items for this product
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get inventory transactions for this product
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    /**
     * Calculate profit margin
     */
    public function profitMargin()
    {
        if (!$this->cost) {
            return null;
        }
        return (($this->price - $this->cost) / $this->price) * 100;
    }

    /**
     * Generate slug from name
     */
    public static function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    /**
     * Find product by SKU or barcode
     */
    public static function findBySKUOrBarcode($value)
    {
        return self::where('sku', $value)->orWhere('barcode', $value)->first();
    }

    /**
     * Track inventory transaction
     */
    public function trackInventory($quantity, $transactionType, $userId, $referenceType = null, $referenceId = null, $notes = null)
    {
        InventoryTransaction::create([
            'product_id' => $this->id,
            'transaction_type' => $transactionType,
            'quantity_change' => $quantity,
            'quantity_after' => $this->stock,
            'user_id' => $userId,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes
        ]);
    }
}
