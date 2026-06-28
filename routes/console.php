<?php

use App\Events\AuctionStatusChanged;
use App\Events\AuctionWon;
use App\Models\Auction;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Scheduler untuk Auto-Update Auction Status
|--------------------------------------------------------------------------
*/

// 1. Scheduler untuk memulai lelang (scheduled → active)
Schedule::call(function () {
    try {
        Log::info('[Scheduler] Checking for auctions to start...');

        $auctions = Auction::where('status', 'scheduled')
            ->where('starts_at', '<=', now())
            ->get();

        if ($auctions->isEmpty()) {
            Log::info('Tidak ada lelang yang harus dimulai');
            return;
        }

        Log::info("Found {$auctions->count()} auctions to start");

        foreach ($auctions as $auction) {
            $updated = Auction::where('id', $auction->id)
                ->where('status', 'scheduled')
                ->update(['status' => 'active']);

            if ($updated) {
                $auction->refresh();
                Log::info("✅ Lelang #{$auction->id} '{$auction->title}' dimulai");
                
                // Broadcast event ke WebSocket
                broadcast(new AuctionStatusChanged($auction->id, 'active'))->toOthers();
                Log::info("📡 Broadcasted AuctionStatusChanged for auction #{$auction->id}");
            }
        }

    } catch (\Exception $e) {
        Log::error('[Scheduler Start] Error: ' . $e->getMessage());
        Log::error('Stack: ' . $e->getTraceAsString());
    }
})->everyMinute();

// 2. Scheduler untuk mengakhiri lelang (active → ended)
Schedule::call(function () {
    try {
        Log::info('[Scheduler] Checking for auctions to end...');

        $auctions = Auction::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();

        if ($auctions->isEmpty()) {
            Log::info('Tidak ada lelang yang harus berakhir');
            return;
        }

        Log::info("Found {$auctions->count()} auctions to end");

        foreach ($auctions as $auction) {
            try {
                DB::transaction(function () use ($auction) {
                    // Cari pemenang (bid tertinggi)
                    $winner = $auction->bids()
                        ->orderBy('amount', 'desc')
                        ->lockForUpdate()
                        ->first();

                    // Update auction
                    $auction->update([
                        'status' => 'ended',
                        'winner_id' => $winner?->user_id,
                        'final_price' => $winner?->amount,
                        'ended_at' => now()
                    ]);

                    Log::info("✅ Lelang #{$auction->id} '{$auction->title}' selesai", [
                        'winner_id' => $winner?->user_id,
                        'final_price' => $winner?->amount
                    ]);
                    
                    // Broadcast events
                    broadcast(new AuctionStatusChanged($auction->id, 'ended'))->toOthers();
                    Log::info("📡 Broadcasted AuctionStatusChanged (ended) for auction #{$auction->id}");
                    
                    if ($winner) {
                        broadcast(new Auction($auction->id, $winner->user_id, $winner->amount))->toOthers();
                        Log::info("📡 Broadcasted AuctionWon for auction #{$auction->id}");
                    }
                });

            } catch (\Exception $e) {
                Log::error("❌ Error processing auction #{$auction->id}: " . $e->getMessage());
            }
        }

    } catch (\Exception $e) {
        Log::error('[Scheduler End] Error: ' . $e->getMessage());
    }
})->everyMinute();

// 3. Cleanup old auctions (optional - jalankan setiap hari jam 3 pagi)
Schedule::call(function () {
    try {
        $deleted = Auction::where('status', 'ended')
            ->where('ended_at', '<=', now()->subDays(30))
            ->delete();

        if ($deleted > 0) {
            Log::info("️ Cleanup: Deleted {$deleted} old ended auctions");
        }
    } catch (\Exception $e) {
        Log::error('[Scheduler Cleanup] Error: ' . $e->getMessage());
    }
})->dailyAt('03:00');