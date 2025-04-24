<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'reference'               => $this->reference,
            'status'                  => $this->status,
            'total_price'             => $this->total_price,
            'notes'                   => $this->notes,
            'created_at'              => $this->created_at,
            'products'                => $this->ordersProducts->map(function($op) {
                return [
                    'id'           => $op->product->id,
                    'name'         => $op->product->name,
                    'price'        => $op->freeze_price,
                    'quantity'     => $op->quantity,
                    'image_url'    => $op->product->image_url,
                ];
            })->all(),
        ];
    }
}
