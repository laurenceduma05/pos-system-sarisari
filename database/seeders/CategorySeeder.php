<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'is_active' => true,
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'Food and drink items',
                'is_active' => true,
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home and garden supplies',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Outdoors',
                'description' => 'Sports equipment and outdoor gear',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Category::generateSlug($category['name']);
            Category::create($category);
        }
    }
}
