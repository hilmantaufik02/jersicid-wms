<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\Workspace;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable, SoftDeletes;

    // Karena kita menggunakan UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'workspace_default',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // Casting ke Backed Enums
            'role' => UserRole::class,
            'workspace_default' => Workspace::class,
            'status' => UserStatus::class,
        ];
    }

    // Helper untuk pengecekan role cepat
    public function hasRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles);
    }
}
