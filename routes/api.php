<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\InvoiceListController;
use App\Http\Controllers\Api\DashboardController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', fn() => auth()->user());      // auth check
//     Route::get('/ping', fn() => ['ok' => true]);       // health
//     // এখানেই পরে products/customers/invoices CRUD বসবে (JSON return)
// });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ping', fn() => ['message' => 'Sanctum working!']);
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);

    Route::apiResource('products', ProductController::class);
    Route::apiResource('customers', CustomerController::class);

    Route::get('invoices', [InvoiceController::class, 'index']);
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::post('invoices', [InvoiceController::class, 'store']);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf']);
});
