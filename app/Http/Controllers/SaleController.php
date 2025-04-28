<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Carbon\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::select('sales.quantity', 'sales.unit_cost', 'sales.selling_price', 'sales.created_at', 'products.name as product_name')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->orderBy('sales.created_at', 'desc')
            ->get();

        $products = Product::select('id','name')
            ->orderBy('name', 'asc')
            ->get();

        // Convert unit cost and selling price to pounds
        $sales = $sales->map(function ($sale) {
            $sale->unit_cost = $sale->unit_cost / 100;
            $sale->selling_price = $sale->selling_price / 100;
            return $sale;
        });

        return view('coffee-sales', compact('sales','products'));
    }

    public function create(SaleRequest $request)
    {
        $data = $request->validated();

        $product = Product::find($data['product_id']);

        if (!$product) {
            return response()->json(['error' => 'No product found.'], 404);
        }

        try {
            $sellingPrice = SaleService::calculateSellingPrice(
                $data['quantity'],
                money($data['unit_cost'] * 100), // convert to pence
                $product->profit_margin,
                money($product->shipping_cost)
            );

            $sale = Sale::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'] * 100,
                'profit_margin' => $product->profit_margin,
                'shipping_cost' => $product->shipping_cost,
                'selling_price' => round($sellingPrice->getAmount()),
            ]);

            return response()->json([
                'product_name' => $sale->product->name,
                'quantity' => $sale->quantity,
                'unit_cost' => $sale->unit_cost / 100, // convert back to pounds
                'selling_price' => $sale->selling_price / 100, // convert back to pounds
                'created_at' => $sale->created_at_formatted,
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Invalid data provided: ' . $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while creating the sale.',
            ], 500);
        }
    }

    public function calculateSellingPrice(SaleRequest $request)
    {
        try {
            $data = $request->validated();

            $product = Product::find($data['product_id']);

            if (!$product) {
                return response()->json(['error' => 'No product found.'], 404);
            }

            $sellingPrice = SaleService::calculateSellingPrice(
                $data['quantity'],
                money($data['unit_cost'] * 100), // convert to pence
                $product->profit_margin,
                money($product->shipping_cost)
            );

            return response()->json([
                'selling_price' => $sellingPrice->getValue(), // get pound value
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'error' => 'Invalid data provided: ' . $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while calculating the selling price.',
            ], 500);
        }
    }
}
