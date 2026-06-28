<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BidResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'auction_id' => $this->auction_id,
            'bidder_id' => $this->bidder_id,
            'bidder_name' => $this->bidder->name,
            'amount' => (float) $this->amount,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}