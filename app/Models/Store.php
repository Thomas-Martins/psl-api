<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'zipcode',
        'city',
        'phone',
        'email',
        'siret',
    ];

    protected $appends = ['full_address', 'customers_count'];

    public function customers()
    {
        return $this->hasMany(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return $this->address . ', ' . $this->zipcode . ' ' . $this->city;
    }

    public function getCustomersCountAttribute(): int
    {
        return $this->customers_count ?? $this->customers()->count();
    }
}
