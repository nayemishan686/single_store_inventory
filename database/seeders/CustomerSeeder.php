<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Acme Ltd.', 'email' => 'acme@example.com', 'phone' => '01234', 'address' => 'Dhaka'],
            ['name' => 'Beta Corp', 'email' => 'beta@example.com', 'phone' => '05678', 'address' => 'Chittagong'],
        ];
        foreach ($rows as $r) {
            Customer::updateOrCreate(['email' => $r['email']], $r);
        }
    }
}
