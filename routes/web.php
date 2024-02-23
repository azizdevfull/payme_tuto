<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::get('/products/{id}', [HomeController::class, 'show'])->name('products.show');
Route::post('checkout/{id}', [HomeController::class, 'store'])->name('checkout.store');


Route::post('/payme', [PaymeController::class, 'index']);
