<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'role_id',
        'store_id',
        'image_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
    ];

    protected $appends = [
        'identity',
        'role',
        'image_url',
        'store',
        'orders_count'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getRoleAttribute(): string
    {
        $role = $this->relationLoaded('role') ? $this->getRelation('role') : $this->role()->first();

        return $role ? $role->name : '';
    }

    public function getIdentityAttribute(): string
    {
        return $this->lastname . ' ' . $this->firstname;
    }
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : '';
    }

    public function getStoreAttribute()
    {
        return $this->store()->first();
    }

    public function getOrdersCountAttribute(): int
    {
        return $this->orders()->count();
    }
}
