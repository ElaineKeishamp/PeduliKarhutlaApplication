<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'campaign_id', 'user_id', 'need_id', 'qty', 'amount', 'message',
        'is_anonymous', 'payment_method', 'payment_gateway', 'payment_ref', 'snap_token', 'status', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'amount' => 'integer',
            'is_anonymous' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function need(): BelongsTo
    {
        return $this->belongsTo(CampaignNeed::class, 'need_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
