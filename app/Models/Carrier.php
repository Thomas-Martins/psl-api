<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'zipcode',
        "contact_person_firstname",
        "contact_person_lastname",
        "contact_person_phone",
        "contact_person_email"
    ];
}
