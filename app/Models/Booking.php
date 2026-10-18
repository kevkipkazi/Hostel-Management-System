<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'student_id',
        'room_id',
        'preferred_hostel_id',
        'requested_check_in_date',
        'requested_check_out_date',
        'booking_date',
        'status',
        'remarks',
        'processed_by',
    ];

    protected $casts = [
        'requested_check_in_date' => 'date',
        'requested_check_out_date' => 'date',
        'booking_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function preferredHostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'preferred_hostel_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function allocation(): HasOne
    {
        return $this->hasOne(Allocation::class);
    }
}
