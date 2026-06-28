<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'starting_price' => (float) $this->starting_price,
            'bid_increment' => (float) $this->bid_increment,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'status' => $this->status,
            'seller' => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
            ],
            'highest_bid' => (float) $this->getHighestBidAmount(),
            'bids_count' => $this->bids()->count(),
            'highest_bidder' => $this->getHighestBidder()?->name,
            'remaining_seconds' => $this->getRemainingSeconds(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}