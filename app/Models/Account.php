<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $account_number
 * @property string $balance
 * @property string $minimum_balance
 * @property \Illuminate\Support\Carbon|null $low_balance_notified_at
 * @property-read User $user
 */
class Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'minimum_balance',
        'low_balance_notified_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'minimum_balance' => 'decimal:2',
        'low_balance_notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}