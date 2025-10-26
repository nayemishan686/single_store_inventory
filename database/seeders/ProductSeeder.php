<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Keyboard', 'sku' => 'KB-100', 'price' => 1200, 'quantity' => 20],
            ['name' => 'Mouse', 'sku' => 'MS-200', 'price' => 800, 'quantity' => 30],
            ['name' => 'Monitor 24"', 'sku' => 'MN-240', 'price' => 12500, 'quantity' => 10],
        ];
        foreach ($rows as $r) {
            Product::updateOrCreate(['sku' => $r['sku']], $r);
        }
    }
}
