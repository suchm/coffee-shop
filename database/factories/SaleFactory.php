<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;

use App\Services\SaleService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::first() ?? User::factory()->create();
        $product = Product::first() ?? Product::factory()->create();

        $quantity = $this->faker->numberBetween(1, 10);
        $unitCost = money($this->faker->randomFloat(2, 10, 30) * 100); // Convert to pence

        $sellingPrice = SaleService::calculateSellingPrice(
            $quantity,
            $unitCost,
            (float) $product->profit_margin,
            money($product->shipping_cost) // Already in pence
        );

        return [
            'user_id'       => $user->id,
            'product_id'    => $product->id,
            'quantity'      => $quantity,
            'unit_cost'     => $unitCost->getAmount(),
            'profit_margin' => $product->profit_margin,
            'shipping_cost' => $product->shipping_cost,
            'selling_price' => round($sellingPrice->getAmount()),
        ];
    }
}
