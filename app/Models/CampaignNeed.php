<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignNeed extends Model
{
    use HasFactory;

    protected $fillable = ['campaign_id', 'item_name', 'unit', 'unit_price', 'qty_target', 'qty_funded'];

    protected function casts(): array
    {
        return ['unit_price' => 'integer', 'qty_target' => 'integer', 'qty_funded' => 'integer'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
