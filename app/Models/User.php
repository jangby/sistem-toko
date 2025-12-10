<?php

namespace App\Models;

// 1. Tambahkan import ini
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 2. Tambahkan "implements FilamentUser"
class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 3. Tambahkan fungsi ini untuk pengecekan hak akses
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya user dengan role 'admin' yang boleh masuk
        return $this->role === 'admin';
    }
}