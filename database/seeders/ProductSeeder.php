<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::create(['title' => 'Telefon', 'price' => 1000]);
        $product->orders()->create([
            'price' => $product->price,
        ]);
    }
}
