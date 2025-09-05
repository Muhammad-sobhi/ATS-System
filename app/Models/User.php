<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <- add this

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <- include HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_RECRUITER = 'recruiter';
    const ROLE_CANDIDATE = 'candidate';

    public static function roles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_RECRUITER,
            self::ROLE_CANDIDATE,
        ];
    }

    // Relationships
    public function jobs()
    {
        return $this->hasMany(Job::class, 'recruiter_id'); // fixed FK
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'assigned_to');
    }
}
