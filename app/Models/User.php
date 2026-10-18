<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'processed_by');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class, 'allocated_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'recorded_by');
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
