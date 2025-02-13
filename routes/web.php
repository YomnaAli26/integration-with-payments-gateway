<?php

use App\Http\Controllers\FatoorahController;
use App\Http\Controllers\HyperPayController;
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



Route::get('/hyper-pay/pay', [HyperPayController::class, 'payOrder']);
Route::get('/payment-status/{checkoutId}', [HyperPayController::class, 'getPaymentStatus'])
->name('payment.status');

Route::get('/fatoorah/pay', [FatoorahController::class,'payOrder']);
Route::get('/callback',[FatoorahController::class,'callback']);
Route::get('/error', [FatoorahController::class,'error']);

