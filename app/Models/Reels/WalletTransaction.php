<?php

namespace App\Models\Reels;

use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'coin_package_id',
        'episode_id',
        'series_id',
        'payment_id',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'transaction_type',
        'source',
        'status',
        'coins',
        'balance_before',
        'balance_after',
        'amount_rupees',
        'description',
        'meta',
        'verified_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'coin_package_id' => 'integer',
        'episode_id' => 'integer',
        'series_id' => 'integer',
        'coins' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'amount_rupees' => 'integer',
        'verified_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(CoinPackage::class, 'coin_package_id');
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class, 'episode_id');
    }
}
