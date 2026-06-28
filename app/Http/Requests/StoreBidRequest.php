<?php

namespace App\Http\Requests;

use App\Models\Auction;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'auction_id' => 'required|exists:auctions,id',
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $auction = Auction::find($this->auction_id);

            // Check Auction exists
            if (!$auction) {
                $validator->errors()->add('auction_id', 'Lelang tidak ditemukan');
                return;
            }

            // Check Auction is active
            if (!$auction->isActive()) {
                $validator->errors()->add('auction_id', 'Lelang tidak aktif');
                return;
            }

            // Check Bidder is not seller
            if ($auction->seller_id === $this->user()->id) {
                $validator->errors()->add('auction_id', 'Anda tidak bisa menawar lelang milik Anda');
                return;
            }

            // Check Bid amount >= highest_bid + increment
            $highestBid = $auction->getHighestBidAmount();
            $minimumBid = $highestBid + $auction->bid_increment;

            if ($this->amount < $minimumBid) {
                $validator->errors()->add(
                    'amount',
                    "Tawaran minimum adalah " . number_format($minimumBid, 2)
                );
            }
        });
    }
}