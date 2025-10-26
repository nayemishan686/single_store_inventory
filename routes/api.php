<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth:sanctum')->get('/ping', function (Illuminate\Http\Request $request) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Sanctum working!']);
    }
    return response()->json(['message' => 'Sanctum working!']);
});
