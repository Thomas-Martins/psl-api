<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;

class Order extends Model
{

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SHIPPED = 'shipped';

    const STATUS_VALUES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_SHIPPED,
    ];

    /** VAT rate (20 %) – falls back to env/config value */
    public const TAX_RATE = 0.20; // <- fallback only


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

    /**
     * Get the applicable VAT rate for the current locale
     */
    private function taxRate(): float
    {
        $locale = App::getLocale();
        return (float) config("tax.rates.{$locale}", config('tax.rate', self::TAX_RATE));
    }

    /**
     * Calculate the order subtotal
     */
    public function calculateSubtotal(): float
    {
        $this->loadMissing('ordersProducts');

        return round(
            $this->ordersProducts->reduce(
                fn (float $sum, $item) => $sum + ((float) $item->freeze_price) * (int) $item->quantity,
                0.0
            ),
            2
        );
    }

    /**
     * Calculate the VAT amount
     */
    public function calculateTax(): float
    {
        return round($this->calculateSubtotal() * $this->taxRate(), 2);
    }

    /**
     * Calculate the total amount including VAT
     */
    public function calculateTotal(): float
    {
        return round($this->calculateSubtotal() + $this->calculateTax(), 2);
    }
}
