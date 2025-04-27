<?php

use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::redirect('/dashboard', '/sales');

Route::middleware('auth')->group(function () {
    Route::get('/sales/', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales/calculate-selling-price', [SaleController::class, 'calculateSellingPrice'])->name('sales.calculateSellingPrice');
});

Route::get('/shipping-partners', function () {
    return view('shipping-partners');
})->middleware(['auth'])->name('shipping.partners');

require __DIR__.'/auth.php';
