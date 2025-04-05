<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'reference',
        'location',
        'price',
        'stock',
        'category_id',
        'supplier_id',
        'image_path'
    ];


    protected $appends = [
        'image_url',
    ];


    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : '';
    }
}
