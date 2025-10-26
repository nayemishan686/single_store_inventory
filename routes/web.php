<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
})->name('home');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Dashboard', ['section' => 'home']))->name('dashboard');

    Route::get('/products', fn() => Inertia::render('Dashboard', ['section' => 'products']))->name('products.index');
    Route::get('/customers', fn() => Inertia::render('Dashboard', ['section' => 'customers']))->name('customers.index');
    Route::get('/invoices', fn() => Inertia::render('Dashboard', ['section' => 'invoices']))->name('invoices.index');
    Route::get('/invoice-create', fn() => Inertia::render('Dashboard', ['section' => 'create']))->name('invoices.create');
});

require __DIR__ . '/auth.php';
