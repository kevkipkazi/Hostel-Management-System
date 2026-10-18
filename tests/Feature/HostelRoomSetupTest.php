<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HostelRoomSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_creates_the_requested_two_bed_rooms_at_the_hostel_period_fee(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($manager)
            ->post(route('hostels.store'), $this->hostelDetails())
            ->assertRedirect(route('hostels.index'));

        $hostel = Hostel::where('hostel_code', 'NORTH-01')->firstOrFail();
        $rooms = $hostel->rooms()->orderBy('room_number')->get();

        $this->assertCount(3, $rooms);
        $this->assertSame([2], $rooms->pluck('capacity')->unique()->values()->all());
        $this->assertSame(['double'], $rooms->pluck('room_type')->unique()->values()->all());
        $this->assertSame(['9250.00'], $rooms->pluck('period_fee')->unique()->values()->all());
        $this->assertSame(9250.0, (float) $hostel->period_fee);
        $this->assertSame('2027 Semester 1', $hostel->booking_period_name);
        $this->assertSame('2027-01-10', $hostel->booking_period_start->toDateString());
        $this->assertSame('2027-05-15', $hostel->booking_period_end->toDateString());
    }

    public function test_manager_can_change_hostel_room_count_and_period_fee(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);
        $hostel = $this->createHostel();
        $this->createRoom($hostel, 'R-001', 8000);
        $this->createRoom($hostel, 'R-002', 8000);

        $this->actingAs($manager)
            ->put(route('hostels.update', $hostel), array_merge($this->hostelDetails(), [
                'hostel_code' => $hostel->hostel_code,
                'room_count' => 3,
                'period_fee' => 10500,
            ]))
            ->assertRedirect(route('hostels.index'));

        $this->assertSame(3, $hostel->rooms()->count());
        $this->assertSame(['10500.00'], $hostel->rooms()->pluck('period_fee')->unique()->values()->all());
    }

    public function test_hostel_room_count_cannot_remove_a_room_with_booking_history(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);
        $hostel = $this->createHostel();
        $this->createRoom($hostel, 'R-001', 8000);
        $room = $this->createRoom($hostel, 'R-002', 8000);
        Booking::create([
            'student_id' => $this->createStudent()->id,
            'room_id' => $room->id,
            'requested_check_in_date' => '2026-11-01',
            'requested_check_out_date' => '2026-11-08',
            'booking_date' => '2026-10-17',
            'status' => 'cancelled',
        ]);

        $this->actingAs($manager)
            ->put(route('hostels.update', $hostel), array_merge($this->hostelDetails(), [
                'hostel_code' => $hostel->hostel_code,
                'room_count' => 1,
                'period_fee' => 8000,
            ]))
            ->assertSessionHasErrors('room_count');

        $this->assertSame(2, $hostel->rooms()->count());
    }

    public function test_room_creation_always_uses_capacity_two_and_hostel_price(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager']);
        $hostel = $this->createHostel();
        $hostel->update(['period_fee' => 11500]);

        $this->actingAs($manager)
            ->post(route('rooms.store'), [
                'hostel_id' => $hostel->id,
                'room_number' => 'A1',
                'floor_number' => 1,
                'capacity' => 8,
                'room_type' => 'shared',
                'period_fee' => 1,
                'status' => 'available',
            ])
            ->assertRedirect(route('rooms.index'));

        $room = $hostel->rooms()->firstOrFail();
        $this->assertSame(2, $room->capacity);
        $this->assertSame('double', $room->room_type);
        $this->assertSame('11500.00', $room->period_fee);
    }

    private function hostelDetails(): array
    {
        return [
            'hostel_name' => 'North Residence',
            'hostel_code' => 'NORTH-01',
            'location' => 'North campus',
            'gender_category' => 'female',
            'booking_period_name' => '2027 Semester 1',
            'booking_period_start' => '2027-01-10',
            'booking_period_end' => '2027-05-15',
            'room_count' => 3,
            'period_fee' => 9250,
            'description' => 'Female accommodation',
            'status' => 'active',
        ];
    }

    private function createHostel(): Hostel
    {
        return Hostel::create([
            'hostel_name' => 'Test Hostel',
            'hostel_code' => 'TEST-01',
            'location' => 'Test campus',
            'gender_category' => 'mixed',
            'booking_period_name' => '2027 Term 1',
            'booking_period_start' => '2027-01-10',
            'booking_period_end' => '2027-05-15',
            'period_fee' => 8000,
            'status' => 'active',
        ]);
    }

    private function createRoom(Hostel $hostel, string $roomNumber, float $periodFee): Room
    {
        return Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => $roomNumber,
            'capacity' => 2,
            'room_type' => 'double',
            'period_fee' => $periodFee,
            'status' => 'available',
        ]);
    }

    private function createStudent(): Student
    {
        return Student::create([
            'admission_number' => 'ADM-200',
            'first_name' => 'Alex',
            'last_name' => 'Student',
            'gender' => 'female',
            'phone' => '0700000200',
            'email' => 'alex@example.com',
            'course' => 'Computer Science',
            'academic_year' => '2026',
            'status' => 'active',
        ]);
    }
}
