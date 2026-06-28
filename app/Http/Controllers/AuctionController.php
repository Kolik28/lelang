<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    // GET /api/auctions
    public function index()
    {
        $auctions = Auction::whereIn('status', ['scheduled', 'active', 'ended'])
            ->with(['seller', 'bids.bidder'])
            ->orderByDesc('ends_at')
            ->get()
            ->map(function ($auction) {
                // Update status berdasarkan waktu sekarang
                $auction->updateStatusIfNeeded();
                return $auction;
            });

        return AuctionResource::collection($auctions);
    }

    // GET /api/auctions/{id}
    public function show($id)
    {
        $auction = Auction::with(['seller', 'bids.bidder'])
            ->findOrFail($id);

        // Update status berdasarkan waktu sekarang
        $auction->updateStatusIfNeeded();

        return new AuctionResource($auction);
    }

    // POST /api/auctions
    public function store(StoreAuctionRequest $request)
    {
        // Parse waktu
        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = Carbon::parse($request->ends_at);
        $now = Carbon::now();

        // Validasi: starts_at tidak boleh di masa lalu
        if ($startsAt->lessThan($now)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => [
                    'starts_at' => ['Waktu mulai harus di masa depan.']
                ]
            ], 422);
        }

        // Validasi: ends_at harus setelah starts_at
        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => [
                    'ends_at' => ['Waktu berakhir harus setelah waktu mulai.']
                ]
            ], 422);
        }

        // Validasi: minimum durasi lelang (misal 1 jam)
        $durationMinutes = $startsAt->diffInMinutes($endsAt);
        if ($durationMinutes < 60) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => [
                    'ends_at' => ['Durasi lelang minimal 1 jam.']
                ]
            ], 422);
        }

        // Tentukan initial status berdasarkan starts_at
        $status = $startsAt->lessThanOrEqualTo($now) ? 'active' : 'scheduled';

        $auction = Auction::create([
            'seller_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $request->image_url,
            'starting_price' => $request->starting_price,
            'bid_increment' => $request->bid_increment,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => $status,
        ]);

        return (new AuctionResource($auction))->response()->setStatusCode(201);
    }

    // GET /api/my-auctions
    public function myAuctions(Request $request)
    {
        $auctions = Auction::where('seller_id', $request->user()->id)
            ->with(['seller', 'bids.bidder'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($auction) {
                // Update status berdasarkan waktu sekarang
                $auction->updateStatusIfNeeded();
                return $auction;
            });

        return AuctionResource::collection($auctions);
    }

    // DELETE /api/auctions/{id}
    public function destroy($id, Request $request)
    {
        $auction = Auction::findOrFail($id);

        // Check authorization
        if ($auction->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus lelang ini'
            ], 403);
        }

        // Check status - hanya bisa hapus lelang scheduled
        if ($auction->status !== 'scheduled') {
            return response()->json([
                'message' => 'Lelang tidak bisa dihapus setelah dimulai'
            ], 422);
        }

        $auction->delete();

        return response()->json([
            'message' => 'Lelang berhasil dihapus'
        ]);
    }

    // PUT /api/auctions/{id}/end (optional - manual end auction)
    public function end($id, Request $request)
    {
        $auction = Auction::findOrFail($id);

        // Check authorization
        if ($auction->seller_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengakhiri lelang ini'
            ], 403);
        }

        // Check status
        if ($auction->status !== 'active') {
            return response()->json([
                'message' => 'Hanya lelang aktif yang bisa diakhiri'
            ], 422);
        }

        // Update status
        $auction->update([
            'status' => 'ended',
            'ended_at' => Carbon::now(),
        ]);

        // Broadcast event
        broadcast(new \App\Events\AuctionEnded($auction))->toOthers();

        return response()->json([
            'message' => 'Lelang berhasil diakhiri',
            'data' => new AuctionResource($auction)
        ]);
    }
}