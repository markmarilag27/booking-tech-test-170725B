<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::name('bookings.')->prefix('bookings')->middleware('auth:sanctum')->group(function () {
    // list of bookings
    Route::get('/', [BookingController::class, 'index'])->name('index');
});
