<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-0001',
                'address' => '123 Main St, City, State',
                'credit_limit' => 500.00,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '555-0002',
                'address' => '456 Oak Ave, City, State',
                'credit_limit' => 1000.00,
            ],
            [
                'name' => 'Bob Wilson',
                'email' => null,
                'phone' => '555-0003',
                'address' => '789 Elm St, City, State',
                'credit_limit' => 250.00,
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'phone' => '555-0004',
                'address' => null,
                'credit_limit' => 750.00,
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'phone' => null,
                'address' => '321 Pine St, City, State',
                'credit_limit' => 600.00,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
