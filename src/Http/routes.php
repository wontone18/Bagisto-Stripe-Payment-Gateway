<?php

use Illuminate\Support\Facades\Route;
use Wontonee\Stripe\Http\Controllers\StripeController;


Route::group(['middleware' => ['web']], function () {

    Route::get('stripe-redirect', [StripeController::class, 'redirect'])->name('stripe.process');
    Route::get('stripe-success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('stripe-cancel', [StripeController::class, 'failure'])->name('stripe.cancel');
});
