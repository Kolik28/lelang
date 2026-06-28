<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $bid;
    public $auction;

    public function __construct(Bid $bid)
    {
        $this->bid = $bid->load(['bidder', 'auction']);
        $this->auction = $this->bid->auction;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('auction.' . $this->auction->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->bid->id,
            'auction_id' => $this->auction->id,
            'bidder_id' => $this->bid->bidder_id,
            'bidder_name' => $this->bid->bidder->name ?? 'Unknown',
            'amount' => (float) $this->bid->amount,
            'highest_bid' => (float) ($this->auction->getHighestBidAmount() ?? 0),
            'bids_count' => $this->auction->bids()->count(),
            'created_at' => $this->bid->created_at->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'BidPlaced';
    }
}