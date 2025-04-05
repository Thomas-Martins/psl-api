<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'zipcode',
        'city',
        'country',
        'contact_person_firstname',
        'contact_person_lastname',
        'contact_person_phone',
        'contact_person_email',
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }
}
