<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('auctions')->insert([
            [
                'seller_id' => 1,
                'title' => 'iPhone 15 Pro Max 256GB',
                'description' => 'Kondisi 99%, lengkap dengan box dan charger.',
                'image_url' => 'images/iphone15.jpg',
                'starting_price' => 15000000,
                'bid_increment' => 50000,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays(7),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 1,
                'title' => 'MacBook Air M2',
                'description' => 'RAM 16GB SSD 512GB, mulus seperti baru.',
                'image_url' => 'images/macbook-air.jpg',
                'starting_price' => 17000000,
                'bid_increment' => 100000,
                'starts_at' => Carbon::now()->subDay(),
                'ends_at' => Carbon::now()->addDays(5),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'title' => 'PlayStation 5 Digital',
                'description' => 'Jarang dipakai, masih garansi.',
                'image_url' => 'images/ps5.jpg',
                'starting_price' => 6500000,
                'bid_increment' => 50000,
                'starts_at' => Carbon::now()->addDays(2),
                'ends_at' => Carbon::now()->addDays(9),
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 2,
                'title' => 'Nintendo Switch OLED',
                'description' => 'Lengkap dengan aksesoris.',
                'image_url' => 'images/switch.jpg',
                'starting_price' => 3500000,
                'bid_increment' => 25000,
                'starts_at' => Carbon::now()->subDays(10),
                'ends_at' => Carbon::now()->subDays(3),
                'status' => 'ended',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'seller_id' => 3,
                'title' => 'Kamera Sony A6400',
                'description' => 'Body only, shutter count rendah.',
                'image_url' => 'images/sony-a6400.jpg',
                'starting_price' => 9000000,
                'bid_increment' => 50000,
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays(10),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}