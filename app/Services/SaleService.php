<?php

namespace App\Services;

use Akaunting\Money\Money;
use InvalidArgumentException;

class SaleService
{
    /*
     * Calculate the selling price of the product
     *
     * @param int $quantity  The number of units being sold.
     * @param Money $unitCost  The cost per unit.
     * @param float $profitMargin  The profit margin (e.g., 0.2 for 20%).
     * @param Money $shippingCost  The additional shipping cost.
     *
     * @return Money  The final selling price as a Money instance.
     */
    public static function calculateSellingPrice(int $quantity, Money $unitCost, float $profitMargin, Money $shippingCost): Money
    {
        try {
            if ($quantity <= 0 || $unitCost->isNegative() || $profitMargin < 0 || $profitMargin >= 1 || $shippingCost->isNegative()) {
                throw new InvalidArgumentException('Invalid input values: quantity, unit cost, profit margin, and shipping cost must be valid.');
            }

            $cost = $unitCost->multiply($quantity);
            $sellingPrice = $cost->divide(1 - $profitMargin)->add($shippingCost);

            return $sellingPrice;

        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException('Error calculating selling price: ' . $e->getMessage());
        }
    }
}
