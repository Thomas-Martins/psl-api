<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'identity' => $this->identity,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'store' => new StoreResource($this->whenLoaded('store')),
            'image_url' => $this->image_url,
            'orders_count' => $this->orders_count,
            'orders' => $this->whenLoaded('orders', function() {
                return OrderResource::collection($this->orders);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
