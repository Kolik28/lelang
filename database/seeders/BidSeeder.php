<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bids')->insert([
            [
                'auction_id' => 1,
                'bidder_id' => 2,
                'amount' => 15050000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auction_id' => 1,
                'bidder_id' => 3,
                'amount' => 15100000,
                'created_at' => now()->addMinute(),
                'updated_at' => now()->addMinute(),
            ],
            [
                'auction_id' => 1,
                'bidder_id' => 4,
                'amount' => 15200000,
                'created_at' => now()->addMinutes(2),
                'updated_at' => now()->addMinutes(2),
            ],
            [
                'auction_id' => 2,
                'bidder_id' => 3,
                'amount' => 17100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auction_id' => 2,
                'bidder_id' => 5,
                'amount' => 17200000,
                'created_at' => now()->addMinute(),
                'updated_at' => now()->addMinute(),
            ],
            [
                'auction_id' => 3,
                'bidder_id' => 1,
                'amount' => 6550000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auction_id' => 3,
                'bidder_id' => 4,
                'amount' => 6600000,
                'created_at' => now()->addMinute(),
                'updated_at' => now()->addMinute(),
            ],
            [
                'auction_id' => 5,
                'bidder_id' => 2,
                'amount' => 9050000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'auction_id' => 5,
                'bidder_id' => 5,
                'amount' => 9100000,
                'created_at' => now()->addMinute(),
                'updated_at' => now()->addMinute(),
            ],
            [
                'auction_id' => 5,
                'bidder_id' => 4,
                'amount' => 9200000,
                'created_at' => now()->addMinutes(2),
                'updated_at' => now()->addMinutes(2),
            ],
        ]);
    }
}