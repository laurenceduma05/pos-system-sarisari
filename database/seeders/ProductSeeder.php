<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Electronics
            ['category' => 'Electronics', 'sku' => 'ELEC001', 'name' => 'Wireless Headphones', 'price' => 49.99, 'cost' => 25.00, 'stock' => 50, 'barcode' => '1234567890001'],
            ['category' => 'Electronics', 'sku' => 'ELEC002', 'name' => 'USB-C Cable', 'price' => 15.99, 'cost' => 5.00, 'stock' => 200, 'barcode' => '1234567890002'],
            ['category' => 'Electronics', 'sku' => 'ELEC003', 'name' => 'Phone Screen Protector', 'price' => 9.99, 'cost' => 2.00, 'stock' => 300, 'barcode' => '1234567890003'],
            ['category' => 'Electronics', 'sku' => 'ELEC004', 'name' => 'Portable Charger', 'price' => 29.99, 'cost' => 12.00, 'stock' => 30, 'barcode' => '1234567890004'],
            
            // Clothing
            ['category' => 'Clothing', 'sku' => 'CLTH001', 'name' => 'T-Shirt', 'price' => 19.99, 'cost' => 8.00, 'stock' => 100, 'barcode' => '1234567890005'],
            ['category' => 'Clothing', 'sku' => 'CLTH002', 'name' => 'Jeans', 'price' => 59.99, 'cost' => 25.00, 'stock' => 40, 'barcode' => '1234567890006'],
            ['category' => 'Clothing', 'sku' => 'CLTH003', 'name' => 'Socks', 'price' => 4.99, 'cost' => 1.50, 'stock' => 500, 'barcode' => '1234567890007'],
            
            // Food & Beverages
            ['category' => 'Food & Beverages', 'sku' => 'FOOD001', 'name' => 'Bottled Water', 'price' => 1.99, 'cost' => 0.50, 'stock' => 1000, 'barcode' => '1234567890008'],
            ['category' => 'Food & Beverages', 'sku' => 'FOOD002', 'name' => 'Coffee', 'price' => 8.99, 'cost' => 3.00, 'stock' => 150, 'barcode' => '1234567890009'],
            ['category' => 'Food & Beverages', 'sku' => 'FOOD003', 'name' => 'Snack Bar', 'price' => 2.50, 'cost' => 1.00, 'stock' => 800, 'barcode' => '1234567890010'],
            
            // Home & Garden
            ['category' => 'Home & Garden', 'sku' => 'HOME001', 'name' => 'Light Bulb', 'price' => 5.99, 'cost' => 2.00, 'stock' => 200, 'barcode' => '1234567890011'],
            ['category' => 'Home & Garden', 'sku' => 'HOME002', 'name' => 'Plant Pot', 'price' => 12.99, 'cost' => 5.00, 'stock' => 75, 'barcode' => '1234567890012'],
            
            // Sports & Outdoors
            ['category' => 'Sports & Outdoors', 'sku' => 'SPORT001', 'name' => 'Water Bottle', 'price' => 24.99, 'cost' => 10.00, 'stock' => 60, 'barcode' => '1234567890013'],
            ['category' => 'Sports & Outdoors', 'sku' => 'SPORT002', 'name' => 'Tennis Racket', 'price' => 99.99, 'cost' => 40.00, 'stock' => 20, 'barcode' => '1234567890014'],
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);
            
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                $productData['category_id'] = $category->id;
                $productData['slug'] = Product::generateSlug($productData['name']);
                Product::create($productData);
            }
        }
    }
}
