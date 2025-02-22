<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ADMIN = 'admin';
    const GESTIONNAIRE = 'gestionnaire';
    const LOGISTICIEN = 'logisticien';
    const CLIENT = 'client';

    protected $fillable = [
        'name'
    ];
}
