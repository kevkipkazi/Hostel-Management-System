<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hostel extends Model
{
    protected $fillable = [
        'hostel_name',
        'hostel_code',
        'location',
        'period_fee',
        'gender_category',
        'description',
        'status',
        'booking_period_name',
        'booking_period_start',
        'booking_period_end',
    ];

    protected $casts = [
        'period_fee' => 'decimal:2',
        'booking_period_start' => 'date',
        'booking_period_end' => 'date',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'preferred_hostel_id');
    }

    public function acceptsGender(string $gender): bool
    {
        return $this->gender_category === 'mixed' || $this->gender_category === $gender;
    }
}
