<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersProduct extends Model
{
    protected $table = 'orders_products';
    protected $fillable = [
        'command_id',
        'product_id',
        'quantity',
        'freeze_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
