<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relasi ke resep yang dibuat user
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    // Relasi ke favorit user
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Relasi ke rating yang diberikan user
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Helper untuk cek apakah user adalah admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
