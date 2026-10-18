<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'hostel_id',
        'room_number',
        'floor_number',
        'capacity',
        'room_type',
        'period_fee',
        'status',
    ];

    protected $casts = [
        'period_fee' => 'decimal:2',
    ];

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function activeAllocations(): HasMany
    {
        return $this->hasMany(Allocation::class)->where('status', 'active');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availableSpaces(): int
    {
        $hostel = $this->relationLoaded('hostel')
            ? $this->hostel
            : $this->hostel()->first();

        if ($hostel?->booking_period_start && $hostel->booking_period_end) {
            if ($hostel->booking_period_end->lt(now()->startOfDay())) {
                return 0;
            }

            return $this->availableSpacesForPeriod(
                $hostel->booking_period_start->toDateString(),
                $hostel->booking_period_end->toDateString()
            );
        }

        return $this->availableSpacesForPeriod(now()->toDateString(), now()->addDay()->toDateString());
    }

    public function bookedSpaces(): int
    {
        return max(0, $this->capacity - $this->availableSpaces());
    }

    public function availabilityStatus(): string
    {
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }

        if ($this->status === 'full' || $this->availableSpaces() === 0) {
            return 'full';
        }

        return $this->bookedSpaces() > 0 ? 'partially booked' : 'available';
    }

    public function availableSpacesForPeriod(string $checkInDate, ?string $checkOutDate, ?int $ignoreBookingId = null): int
    {
        $allocations = $this->allocations()->where('status', 'active');

        if ($checkOutDate !== null) {
            $allocations->whereDate('check_in_date', '<', $checkOutDate);
        }

        $occupiedAllocations = $allocations
            ->where(function ($query) use ($checkInDate) {
                $query->whereNull('expected_check_out_date')
                    ->orWhereDate('expected_check_out_date', '>', $checkInDate);
            })
            ->count();

        $bookings = $this->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->whereDoesntHave('allocation', fn ($query) => $query->where('status', 'active'));

        if ($ignoreBookingId !== null) {
            $bookings->whereKeyNot($ignoreBookingId);
        }

        if ($checkOutDate !== null) {
            $bookings->whereDate('requested_check_in_date', '<', $checkOutDate);
        }

        $occupiedBookings = $bookings
            ->where(function ($query) use ($checkInDate) {
                $query->whereNull('requested_check_out_date')
                    ->orWhereDate('requested_check_out_date', '>', $checkInDate);
            })
            ->count();

        return max(0, $this->capacity - $occupiedAllocations - $occupiedBookings);
    }
}
