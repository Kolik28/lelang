<?php

namespace App\Events;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auction;

    public function __construct(Auction $auction)
    {
        $this->auction = $auction->load('bids.bidder', 'seller');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('auction.' . $this->auction->id),
        ];
    }

    public function broadcastWith(): array
    {
        $highestBid = $this->auction->getHighestBid();

        return [
            'auction_id' => $this->auction->id,
            'status' => 'ended',
            'winner' => $highestBid ? [
                'id' => $highestBid->bidder->id,
                'name' => $highestBid->bidder->name,
            ] : null,
            'final_price' => $highestBid ? (float) $highestBid->amount : null,
            'message' => $highestBid 
                ? "Pemenang: {$highestBid->bidder->name} dengan harga " . number_format($highestBid->amount, 2)
                : "Lelang berakhir tanpa pemenang",
        ];
    }

    public function broadcastAs(): string
    {
        return 'AuctionEnded';
    }
}