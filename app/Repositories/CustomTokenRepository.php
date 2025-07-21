<?php

namespace App\Repositories;

use Laravel\Passport\TokenRepository as PassportTokenRepository;

class CustomTokenRepository extends PassportTokenRepository
{
    public function create($attributes)
    {
        if (isset($attributes['revoked'])) {
            $attributes['revoked'] = (bool) $attributes['revoked'];
        }
        return parent::create($attributes);
    }
}
