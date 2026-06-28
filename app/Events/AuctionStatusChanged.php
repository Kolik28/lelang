<?php

namespace App\Events;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AuctionStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $auctionId;
    public $newStatus;
    public $auction;

    public function __construct($auctionId, $newStatus)
    {
        $this->auctionId = $auctionId;
        $this->newStatus = $newStatus;
        $this->auction = Auction::find($auctionId);
        
        Log::info('📢 AuctionStatusChanged Event', [
            'auction_id' => $auctionId,
            'new_status' => $newStatus,
        ]);
    }

    public function broadcastOn(): array
    {
        Log::info('📡 Broadcasting on channels: auctions, auction.' . $this->auctionId);
        
        return [
            new Channel('auctions'),
            new Channel('auction.' . $this->auctionId),
        ];
    }

    public function broadcastWith(): array
    {
        $data = [
            'auction_id' => $this->auctionId,
            'new_status' => $this->newStatus,
        ];

        if ($this->auction) {
            $data['auction'] = [
                'id' => $this->auction->id,
                'title' => $this->auction->title,
                'status' => $this->newStatus,
            ];
        }

        Log::info('📦 Data yang di-broadcast:', $data);
        
        return $data;
    }

    public function broadcastAs(): string
    {
        return 'AuctionStatusChanged';
    }
}