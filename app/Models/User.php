<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'email',
        'password',
        'role',
        'safety_pin_hash',
        'duress_pin_hash',
        'is_pin_setup',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'safety_pin_hash',
        'duress_pin_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'safety_pin_hash' => 'hashed',
            'duress_pin_hash' => 'hashed',
            'is_pin_setup' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
