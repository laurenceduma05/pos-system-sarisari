<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user with the role of ADMIN
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com', // You can change this email
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'), // Use the Hash facade to securely store the password
            'role' => RoleType::ADMIN, // Assuming `RoleType::ADMIN` is a constant or enum for admin role
            'remember_token' => Str::random(10),
        ]);
        
        // Optionally create a regular user
        DB::table('users')->insert([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Regular user password
            'role' => RoleType::USER, // Regular user role
            'remember_token' => Str::random(10),
        ]);
    }
}
