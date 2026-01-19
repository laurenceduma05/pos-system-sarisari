<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Product Information & Identification
            $table->string('brand')->nullable()->after('barcode');
            $table->string('product_image')->nullable()->after('brand');
            $table->string('size')->nullable()->after('product_image');
            $table->string('color')->nullable()->after('size');
            $table->decimal('weight', 8, 2)->nullable()->after('color');
            $table->string('location_bin')->nullable()->after('weight');
            $table->integer('reorder_point')->default(0)->after('location_bin');
            $table->integer('max_stock_level')->default(0)->after('reorder_point');
            $table->string('tags')->nullable()->after('max_stock_level');
            
            // Supplier/Vendor Management
            $table->string('supplier_name')->nullable()->after('tags');
            $table->string('supplier_contact')->nullable()->after('supplier_name');
            $table->string('supplier_sku')->nullable()->after('supplier_contact');
            $table->integer('lead_time_days')->nullable()->after('supplier_sku');
            $table->boolean('preferred_vendor')->default(false)->after('lead_time_days');
            $table->timestamp('last_purchase_date')->nullable()->after('preferred_vendor');
            $table->decimal('last_purchase_price', 10, 2)->nullable()->after('last_purchase_date');
            
            // Unit of Measure (UOM)
            $table->string('purchase_unit')->default('piece')->after('last_purchase_price');
            $table->string('sales_unit')->default('piece')->after('purchase_unit');
            $table->decimal('uom_conversion_factor', 8, 2)->default(1)->after('sales_unit');
            $table->string('packaging_type')->nullable()->after('uom_conversion_factor');
            
            // Pricing & Financial
            $table->decimal('profit_margin_percent', 5, 2)->nullable()->after('packaging_type');
            $table->string('tax_category')->nullable()->after('profit_margin_percent');
            $table->boolean('discount_eligible')->default(true)->after('tax_category');
            $table->decimal('wholesale_price', 10, 2)->nullable()->after('discount_eligible');
            $table->decimal('msrp', 10, 2)->nullable()->after('wholesale_price');
            
            // Tracking & Analytics
            $table->date('expiration_date')->nullable()->after('msrp');
            $table->string('batch_lot_number')->nullable()->after('expiration_date');
            $table->string('serial_number')->nullable()->after('batch_lot_number');
            $table->string('warranty_info')->nullable()->after('serial_number');
            $table->enum('product_status', ['active', 'discontinued', 'seasonal', 'on_order'])->default('active')->after('warranty_info');
            $table->timestamp('date_added')->useCurrent()->after('product_status');
            $table->timestamp('last_modified')->useCurrent()->useCurrentOnUpdate()->after('date_added');
            $table->timestamp('last_sold_date')->nullable()->after('last_modified');
            $table->decimal('average_daily_sales', 8, 2)->default(0)->after('last_sold_date');
            $table->enum('sales_velocity', ['slow', 'medium', 'fast'])->default('medium')->after('average_daily_sales');
            
            // Multi-Location Features
            $table->json('quantity_by_location')->nullable()->after('sales_velocity');
            
            // E-commerce Integration
            $table->boolean('available_online')->default(false)->after('quantity_by_location');
            $table->longText('online_description')->nullable()->after('available_online');
            $table->string('meta_tags')->nullable()->after('online_description');
            
            // Advanced Features
            $table->json('composite_items')->nullable()->after('meta_tags');
            $table->string('alternative_products')->nullable()->after('composite_items');
            $table->boolean('seasonal')->default(false)->after('alternative_products');
            $table->json('promotional_pricing')->nullable()->after('seasonal');
            $table->decimal('commission_rate', 5, 2)->nullable()->after('promotional_pricing');
            $table->boolean('alerts_enabled')->default(true)->after('commission_rate');
            $table->longText('internal_notes')->nullable()->after('alerts_enabled');
            
            // Reporting & Compliance
            $table->string('product_group')->nullable()->after('internal_notes');
            $table->decimal('cogs', 10, 2)->nullable()->after('product_group');
            $table->enum('inventory_valuation_method', ['FIFO', 'LIFO', 'WeightedAverage'])->default('FIFO')->after('cogs');
            $table->string('hs_code')->nullable()->after('inventory_valuation_method');
            $table->string('country_of_origin')->nullable()->after('hs_code');
            $table->string('compliance_certifications')->nullable()->after('country_of_origin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop all new columns
            $table->dropColumn([
                'brand', 'product_image', 'size', 'color', 'weight', 'location_bin', 
                'reorder_point', 'max_stock_level', 'tags',
                'supplier_name', 'supplier_contact', 'supplier_sku', 'lead_time_days', 
                'preferred_vendor', 'last_purchase_date', 'last_purchase_price',
                'purchase_unit', 'sales_unit', 'uom_conversion_factor', 'packaging_type',
                'profit_margin_percent', 'tax_category', 'discount_eligible', 
                'wholesale_price', 'msrp',
                'expiration_date', 'batch_lot_number', 'serial_number', 'warranty_info', 
                'product_status', 'date_added', 'last_modified', 'last_sold_date', 
                'average_daily_sales', 'sales_velocity',
                'quantity_by_location',
                'available_online', 'online_description', 'meta_tags',
                'composite_items', 'alternative_products', 'seasonal', 'promotional_pricing', 
                'commission_rate', 'alerts_enabled', 'internal_notes',
                'product_group', 'cogs', 'inventory_valuation_method', 'hs_code', 
                'country_of_origin', 'compliance_certifications'
            ]);
        });
    }
};
