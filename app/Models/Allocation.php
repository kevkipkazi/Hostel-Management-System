<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $fillable = [
        'student_id',
        'room_id',
        'booking_id',
        'allocated_by',
        'allocation_date',
        'check_in_date',
        'expected_check_out_date',
        'actual_check_out_date',
        'status',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'check_in_date' => 'date',
        'expected_check_out_date' => 'date',
        'actual_check_out_date' => 'date',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function allocatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
