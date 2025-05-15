<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_SHIPPED = 'shipped';


    protected $fillable = [
        'user_id',
        'carrier_id',
        'status',
        'reference',
        'estimated_delivery_date',
        'departure_date',
        'arrival_date',
        'total_price',
        'cancellation_reason',
        'notes',
        'invoiced',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ordersProducts(): HasMany
    {
        return $this->hasMany(OrdersProduct::class);
    }

    public function statusLabels(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_SHIPPED,
        ];
    }
}
