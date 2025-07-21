<?php

namespace App\Models\Passport;

use Laravel\Passport\Token as PassportToken;

class Token extends PassportToken
{
    protected $casts = [
        'revoked' => 'boolean',
        'scopes' => 'array',
        'expires_at' => 'datetime',
    ];

    public function setRevokedAttribute($value)
    {
        $this->attributes['revoked'] = $value ? 'true' : 'false';
    }
}
