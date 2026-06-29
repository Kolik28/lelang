<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuctionRequest;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\AuctionEnded;

class AuctionController extends Controller
{
    // GET /api/auctions
    public function index()
    {
        $auctions = Auction::whereIn('status', ['scheduled', 'active', 'ended'])
            ->with(['seller', 'bids.bidder'])
            ->orderByDesc('ends_at')
            ->get()
            ->map(fn($auction) => tap($auction)->updateStatusIfNeeded());

        return AuctionResource::collection($auctions);
    }

    // GET /api/auctions/{id}
    public function show($id)
    {
        $auction = Auction::with(['seller', 'bids.bidder'])->findOrFail($id);
        $auction->updateStatusIfNeeded();
        return new AuctionResource($auction);
    }

    // POST /api/auctions
    public function store(StoreAuctionRequest $request)
    {
        $startsAt = Carbon::parse($request->starts_at);
        $endsAt = Carbon::parse($request->ends_at);
        $now = Carbon::now();

        if ($startsAt->diffInMinutes($endsAt) < 60) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => [
                    'ends_at' => ['Durasi lelang minimal 1 jam.']
                ]
            ], 422);
        }

        $imageUrl = null;

        if ($request->hasFile('image')) {
            $host = $request->getSchemeAndHttpHost();
            $path = $request->file('image')->store('auctions', 'public');
            $imageUrl = $host . Storage::url($path);
        } elseif (filled($request->image_url)) {
            $imageUrl = $request->image_url;
        }

        $status = $startsAt->lessThanOrEqualTo($now) ? 'active' : 'scheduled';

        $auction = Auction::create([
            'seller_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $imageUrl,
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
            ->map(fn($auction) => tap($auction)->updateStatusIfNeeded());

        return AuctionResource::collection($auctions);
    }

    // DELETE /api/auctions/{id}
    public function destroy($id, Request $request)
    {
        $auction = Auction::findOrFail($id);

        if ($auction->seller_id !== $request->user()->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menghapus lelang ini'], 403);
        }

        if ($auction->status !== 'scheduled') {
            return response()->json(['message' => 'Lelang tidak bisa dihapus setelah dimulai'], 422);
        }

        if ($auction->image_url && str_contains($auction->image_url, '/storage/auctions/')) {
            $path = str_replace('/storage/', '', $auction->image_url);
            Storage::disk('public')->delete($path);
        }

        $auction->delete();

        return response()->json(['message' => 'Lelang berhasil dihapus']);
    }

    // PUT /api/auctions/{id}/end
    public function end($id, Request $request)
    {
        $auction = Auction::findOrFail($id);

        if ($auction->seller_id !== $request->user()->id) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengakhiri lelang ini'], 403);
        }

        if ($auction->status !== 'active') {
            return response()->json(['message' => 'Hanya lelang aktif yang bisa diakhiri'], 422);
        }

        $auction->update([
            'status' => 'ended',
            'ended_at' => Carbon::now(),
        ]);

        broadcast(new AuctionEnded($auction))->toOthers();

        return response()->json([
            'message' => 'Lelang berhasil diakhiri',
            'data' => new AuctionResource($auction)
        ]);
    }
}