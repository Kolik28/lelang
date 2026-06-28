<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'image_url',
        'starting_price',
        'bid_increment',
        'starts_at',
        'ends_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'bid_increment' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Auction belongs to one seller (User)
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Auction has many bids
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->orderByDesc('created_at');
    }

    // ========== ACCESSORS & MUTATORS ==========

    /**
     * Automatically calculate status based on timestamps
     * scheduled -> active -> ended/completed
     */
    public function getStatusAttribute($value)
    {
        $now = now();

        // Jika status di DB sudah marked sebagai ended/cancelled/completed, return as-is
        if (in_array($value, ['ended', 'completed', 'cancelled'])) {
            return $value;
        }

        // Jika sekarang belum mencapai starts_at -> scheduled
        if ($now->lt($this->starts_at)) {
            return 'scheduled';
        }

        // Jika sekarang antara starts_at dan ends_at -> active
        if ($now->between($this->starts_at, $this->ends_at)) {
            return 'active';
        }

        // Jika sudah lewat ends_at -> ended
        return 'ended';
    }

    // ========== HELPER METHODS ==========

    /**
     * Get highest bid record
     */
    public function getHighestBid()
    {
        return $this->bids()->first();
    }

    /**
     * Get highest bid amount
     */
    public function getHighestBidAmount()
    {
        $bid = $this->getHighestBid();
        return $bid ? (float) $bid->amount : (float) $this->starting_price;
    }

    /**
     * Get highest bidder (User)
     */
    public function getHighestBidder()
    {
        return $this->getHighestBid()?->bidder;
    }

    /**
     * Get bids count
     */
    public function getBidsCount(): int
    {
        return $this->bids()->count();
    }

    /**
     * Check if auction is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if auction is ended
     */
    public function isEnded(): bool
    {
        return $this->status === 'ended' || $this->status === 'completed';
    }

    /**
     * Check if auction is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Calculate remaining time in seconds
     */
    public function getRemainingSeconds(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->ends_at, false));
    }

    /**
     * Calculate remaining time in minutes
     */
    public function getRemainingMinutes(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return max(0, now()->diffInMinutes($this->ends_at, false));
    }

    /**
     * Update status if needed (call manually or from job/scheduler)
     */
    public function updateStatusIfNeeded(): void
    {
        $now = now();

        // Jika active dan sudah lewat ends_at, ubah ke ended
        if ($this->isActive() && $now->greaterThanOrEqualTo($this->ends_at)) {
            $this->update([
                'status' => 'ended',
                'ended_at' => $now,
            ]);
        }
        // Jika scheduled dan sudah lewat starts_at, ubah ke active
        elseif ($this->isScheduled() && $now->greaterThanOrEqualTo($this->starts_at)) {
            $this->update([
                'status' => 'active',
            ]);
        }
    }

    /**
     * Format remaining time for display (e.g., "2d 5h 30m")
     */
    public function getFormattedRemainingTime(): string
    {
        if (!$this->isActive()) {
            return 'Lelang tidak aktif';
        }

        $diff = now()->diff($this->ends_at);

        if ($diff->days > 0) {
            return "{$diff->days}d {$diff->h}h {$diff->i}m";
        } elseif ($diff->h > 0) {
            return "{$diff->h}h {$diff->i}m {$diff->s}s";
        } elseif ($diff->i > 0) {
            return "{$diff->i}m {$diff->s}s";
        } else {
            return "{$diff->s}s";
        }
    }
}