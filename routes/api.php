<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\BidController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public auction viewing
Route::get('/auctions', [AuctionController::class, 'index']);
Route::get('/auctions/{id}', [AuctionController::class, 'show']);
Route::get('/auctions/{id}/bids', [BidController::class, 'getAuctionBids']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Auctions
    Route::post('/auctions', [AuctionController::class, 'store']);
    Route::get('/my-auctions', [AuctionController::class, 'myAuctions']);
    Route::delete('/auctions/{id}', [AuctionController::class, 'destroy']);

    // Bids
    Route::post('/bids', [BidController::class, 'store']);
    Route::get('/my-bids', [BidController::class, 'myBids']);
});