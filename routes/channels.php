<?php

use Illuminate\Support\Facades\Broadcast;

// Kanal publik per lelang
Broadcast::channel('auction.{auctionId}', function ($user, $auctionId) {
    return true; // Publik atau sesuaikan otorisasi jika diperlukan
});

// Kanal privat personal
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// (Bonus) Presence channel
Broadcast::channel('presence-auction.{auctionId}', function ($user, $auctionId) {
    return ['id' => $user->id, 'name' => $user->name];
});