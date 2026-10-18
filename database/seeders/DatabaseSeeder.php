<?php

namespace Database\Seeders;

use App\Models\Allocation;
use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        $admin = User::where('role', 'administrator')->first();

        $student1 = Student::create([
            'admission_number' => 'ADM-001',
            'first_name' => 'James',
            'last_name' => 'Oduor',
            'gender' => 'male',
            'phone' => '0700123456',
            'email' => 'james@example.com',
            'course' => 'Computer Science',
            'department' => 'ICT',
            'academic_year' => '2026',
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'admission_number' => 'ADM-002',
            'first_name' => 'Grace',
            'last_name' => 'Njeri',
            'gender' => 'female',
            'phone' => '0700654321',
            'email' => 'grace@example.com',
            'course' => 'Business Studies',
            'department' => 'Commerce',
            'academic_year' => '2026',
            'status' => 'active',
        ]);

        $hostel1 = Hostel::create([
            'hostel_name' => 'Apex Hostel',
            'hostel_code' => 'APX-01',
            'location' => 'Main campus',
            'booking_period_name' => '2026 Term 2',
            'booking_period_start' => now()->toDateString(),
            'booking_period_end' => now()->addMonths(6)->toDateString(),
            'period_fee' => 15000,
            'gender_category' => 'male',
            'status' => 'active',
            'description' => 'Male hostel for first-year students',
        ]);

        $hostel2 = Hostel::create([
            'hostel_name' => 'Pearl Residence',
            'hostel_code' => 'PRL-02',
            'location' => 'North wing',
            'booking_period_name' => '2026 Term 2',
            'booking_period_start' => now()->toDateString(),
            'booking_period_end' => now()->addMonths(6)->toDateString(),
            'period_fee' => 18000,
            'gender_category' => 'female',
            'status' => 'active',
            'description' => 'Female residence with Wi-Fi',
        ]);

        $room1 = Room::create([
            'hostel_id' => $hostel1->id,
            'room_number' => 'A1',
            'floor_number' => 1,
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => 15000,
            'status' => 'available',
        ]);

        $room2 = Room::create([
            'hostel_id' => $hostel2->id,
            'room_number' => 'F1',
            'floor_number' => 1,
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => 18000,
            'status' => 'available',
        ]);

        $booking = Booking::create([
            'student_id' => $student1->id,
            'room_id' => $room1->id,
            'preferred_hostel_id' => $hostel1->id,
            'requested_check_in_date' => now()->toDateString(),
            'requested_check_out_date' => now()->addMonths(6)->toDateString(),
            'booking_date' => now()->toDateString(),
            'status' => 'approved',
            'processed_by' => $admin->id,
        ]);

        $allocation = Allocation::create([
            'student_id' => $student1->id,
            'room_id' => $room1->id,
            'booking_id' => $booking->id,
            'allocated_by' => $admin->id,
            'allocation_date' => now()->toDateString(),
            'check_in_date' => now()->toDateString(),
            'expected_check_out_date' => now()->addMonths(6)->toDateString(),
            'status' => 'active',
        ]);

        Payment::create([
            'student_id' => $student1->id,
            'allocation_id' => $allocation->id,
            'receipt_number' => 'RCPT-1001',
            'amount_paid' => 15000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'bank',
            'transaction_reference' => 'TXN-1001',
            'payment_period' => '2026-10',
            'recorded_by' => $admin->id,
            'remarks' => 'Initial room payment',
        ]);
    }
}
