<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class ChequeIssuancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Initialize Faker and Carbon
        $faker = Faker::create();
        $now = Carbon::now();
        $twoWeeksAgo = Carbon::now()->subWeeks(2);

        // Generate 10 random cheque issuance records (or change the number)
        foreach (range(1, 10) as $index) {
            DB::table('cheque_issuances')->insert([
                'business_name' => $faker->company,
                'bank' => $faker->company,
                'cheque_number' => $faker->numerify('####'),  // Last 4 digits
                'amount' => $faker->randomFloat(2, 100, 10000),  // Random amount between 100 and 10000
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),  // Random status
                'remarks' => $faker->sentence,  // Random remarks
                'created_at' => $faker->dateTimeBetween($twoWeeksAgo, $now),
                'updated_at' => $faker->dateTimeBetween($twoWeeksAgo, $now),
            ]);
        }
    }
}
