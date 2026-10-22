<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 'image',
        'location_name', 'latitude', 'longitude', 'alert_level', 'affected_people', 'affected_type',
        'target_amount', 'collected_amount', 'deadline', 'status', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'affected_people' => 'integer',
            'target_amount' => 'integer',
            'collected_amount' => 'integer',
            'deadline' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fundraiser(): BelongsTo
    {
        return $this->user();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function needs(): HasMany
    {
        return $this->hasMany(CampaignNeed::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class);
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(Disbursement::class);
    }

    protected function progress(): Attribute
    {
        return Attribute::get(fn (): float => $this->target_amount > 0
            ? min(100, round(($this->collected_amount / $this->target_amount) * 100, 1))
            : 0);
    }

    protected function daysLeft(): Attribute
    {
        return Attribute::get(fn (): int => Carbon::today()->diffInDays($this->deadline, false));
    }

    protected function isOpen(): Attribute
    {
        return Attribute::get(fn (): bool => $this->status === 'active' && $this->deadline->copy()->endOfDay()->isFuture());
    }

    protected function totalDisbursed(): Attribute
    {
        return Attribute::get(fn (): int => (int) $this->disbursements()
            ->where('status', 'approved')->sum('amount'));
    }

    protected function remainingFunds(): Attribute
    {
        return Attribute::get(function (): int {
            $reserved = (int) $this->disbursements()
                ->whereIn('status', ['approved', 'pending'])->sum('amount');

            return max(0, (int) $this->collected_amount - $reserved);
        });
    }
}
