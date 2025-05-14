<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'reference' => $this->reference,
            'location' => $this->location,
            'stock' => $this->stock,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'supplier' => [
                'id' => $this->supplier->id,
                'name' => $this->supplier->name,
            ],
        ];
    }
}
