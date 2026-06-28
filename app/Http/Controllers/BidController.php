<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Events\BidderOutbid;
use App\Http\Requests\StoreBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BidController extends Controller
{
    // POST /api/bids
    public function store(StoreBidRequest $request)
    {
        $auction = Auction::with(['bids', 'seller'])->findOrFail($request->auction_id);
        $userId = $request->user()->id;

        // Validasi auction status
        if ($auction->status !== 'active') {
            return response()->json([
                'message' => 'Lelang tidak aktif',
            ], 422);
        }

        // Validasi seller tidak bisa bid sendiri
        if ($auction->seller_id === $userId) {
            return response()->json([
                'message' => 'Anda tidak bisa membuat penawaran pada lelang milik Anda',
            ], 422);
        }

        try {
            // Use transaction
            $bid = DB::transaction(function () use ($request, $auction, $userId) {
                // Validasi bid amount
                $highestBid = $auction->getHighestBidAmount();
                $minimumBid = $highestBid + $auction->bid_increment;

                if ($request->amount < $minimumBid) {
                    throw ValidationException::withMessages([
                        'amount' => "Penawaran minimum adalah Rp " . number_format($minimumBid, 0, ',', '.')
                    ]);
                }

                // Create bid
                $bid = Bid::create([
                    'auction_id' => $auction->id,
                    'bidder_id' => $userId,
                    'amount' => $request->amount,
                ]);

                // Lock untuk update
                $auction->lockForUpdate();

                return $bid;
            });

            // Broadcast BidPlaced event
            broadcast(new BidPlaced($bid))->toOthers();

            // Get bidders who need to be notified (all except current bidder)
            $previousBidders = Bid::where('auction_id', $auction->id)
                ->where('bidder_id', '!=', $bid->bidder_id)
                ->distinct('bidder_id')
                ->pluck('bidder_id');

            // Notify previous bidders they were outbid
            foreach ($previousBidders as $bidderId) {
                $bidder = User::find($bidderId);
                if ($bidder) {
                    broadcast(new BidderOutbid(
                        $bidder,
                        $auction->title,
                        $bid->amount
                    ))->toOthers();
                }
            }

            return response()->json([
                'message' => 'Penawaran berhasil ditempatkan',
                'data' => new BidResource($bid)
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat penawaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/auctions/{id}/bids
    public function getAuctionBids($auctionId)
    {
        $auction = Auction::findOrFail($auctionId);

        $bids = Bid::where('auction_id', $auctionId)
            ->with(['bidder:id,name,email'])
            ->orderByDesc('created_at')
            ->get();

        return BidResource::collection($bids);
    }

    // GET /api/my-bids
    public function myBids(Request $request)
    {
        $bids = Bid::where('bidder_id', $request->user()->id)
            ->with([
                'auction:id,title,ends_at,status',
                'bidder:id,name,email'
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        return BidResource::collection($bids);
    }
}