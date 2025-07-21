<?php

namespace App\Models\Passport;

use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    public function setPersonalAccessClientAttribute($value)
    {
        $this->attributes['personal_access_client'] = $value ? 'true' : 'false';
    }
    public function setPasswordClientAttribute($value)
    {
        $this->attributes['password_client'] = $value ? 'true' : 'false';
    }
    public function setRevokedAttribute($value)
    {
        $this->attributes['revoked'] = $value ? 'true' : 'false';
    }
}
