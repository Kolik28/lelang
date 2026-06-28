<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidderOutbid implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $auctionTitle;
    public $newHighestBid;

    public function __construct(User $user, $auctionTitle, $newHighestBid)
    {
        $this->user = $user;
        $this->auctionTitle = $auctionTitle;
        $this->newHighestBid = $newHighestBid;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Anda telah dikalahkan di lelang ' . $this->auctionTitle,
            'new_highest_bid' => $this->newHighestBid,
            'auction_title' => $this->auctionTitle,
        ];
    }

    public function broadcastAs(): string
    {
        return 'BidderOutbid';
    }
}