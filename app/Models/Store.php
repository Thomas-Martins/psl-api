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

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
