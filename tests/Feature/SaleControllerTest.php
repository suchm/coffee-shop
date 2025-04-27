<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsSalesList()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange
        Sale::factory()->create([
            'quantity' => 10,
            'unit_cost' => 500,  // 5 pounds
            'selling_price' => 7667,  // 76.67 pounds
        ]);

        // Act
        $response = $this->get(route('sales.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('sales');
    }

    public function testCreateValidSale()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange
        $product = Product::factory()->create();
        $data = [
            'quantity' => 10,
            'unit_cost' => 5.00,
        ];

        // Act
        $response = $this->postJson(route('sales.create'), $data);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'quantity' => 10,
            'unit_cost' => 5.00,
            'selling_price' => 76.67,
        ]);
    }

    public function testCreateSaleWithNoProductFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulate no product available
        Product::query()->delete();

        $data = [
            'quantity' => 10,
            'unit_cost' => 5.00,
        ];

        $response = $this->postJson(route('sales.create'), $data);

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'No product found.',
        ]);
    }

    public function testCreateSaleWithInvalidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $data = [
            'quantity' => -10,
            'unit_cost' => 'invalid',
        ];

        $response = $this->postJson(route('sales.create'), $data);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'quantity' => ['The quantity must be at least 1.'],
                'unit_cost' => ['The unit cost must be a number.'],
            ]
        ]);
    }

    public function testCalculateSellingPriceValidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $data = [
            'quantity' => 10,
            'unit_cost' => 5.00,
        ];

        $response = $this->postJson(route('sales.calculateSellingPrice'), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'selling_price' => 76.67,
        ]);
    }

    public function testCalculateSellingPriceWithNoProduct()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Product::query()->delete();

        $data = [
            'quantity' => 10,
            'unit_cost' => 5.00,
        ];

        $response = $this->postJson(route('sales.calculateSellingPrice'), $data);

        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'No product found.',
        ]);
    }

    public function testCalculateSellingPriceWithInvalidData()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'quantity' => -10,
            'unit_cost' => 'invalid',
        ];

        $response = $this->postJson(route('sales.calculateSellingPrice'), $data);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'quantity' => ['The quantity must be at least 1.'],
                'unit_cost' => ['The unit cost must be a number.'],
            ]
        ]);
    }
}
