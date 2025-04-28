<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Sales Agent',
            'email' => 'sales@coffee.shop',
        ]);

        Product::factory()->create();
        Product::create([
            'name' => 'Arabic coffee',
            'profit_margin' => 0.15,
            'shipping_cost' => 1000, // In pence
        ]);

        //        Sale::factory()->count(10)->create();
    }
}
