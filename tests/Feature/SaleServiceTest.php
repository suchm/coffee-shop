<?php

namespace Tests\Feature;

use App\Services\SaleService;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    /**
     * Test the calculateSellingPrice method with normal values.
     *
     * @return void
     */
    public function testCalculateSellingPriceWithValidData()
    {
        $quantity = 1;
        $unitCost = money(1000); // £10.00 per unit
        $profitMargin = 0.25; // 25% profit margin
        $shippingCost = money(1000); // £10.00 shipping cost

        $expectedSellingPrice = money(2333); // Expected £23.33 per unit

        // Calculate the selling price using the SaleService
        $sellingPrice = SaleService::calculateSellingPrice($quantity, $unitCost, $profitMargin, $shippingCost);

        $this->assertEquals($expectedSellingPrice->getAmount(), round($sellingPrice->getAmount()));
        $this->assertEquals($expectedSellingPrice->getCurrency(), $sellingPrice->getCurrency());
    }

    /**
     * Test the calculateSellingPrice method with invalid (negative) values.
     *
     * @return void
     */
    public function testCalculateSellingPriceWithInvalidNegativeValues()
    {
        $quantity = -10; // Invalid negative quantity
        $unitCost = money(1000); // £10.00 per unit
        $profitMargin = 0.25; // 25% profit margin
        $shippingCost = money(1000); // £10.00 shipping cost

        $this->expectException(\InvalidArgumentException::class);

        // This should throw an exception due to invalid quantity
        SaleService::calculateSellingPrice($quantity, $unitCost, $profitMargin, $shippingCost);
    }
}
