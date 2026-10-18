<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudentBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_log_in_and_create_a_booking_for_their_own_profile(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);
        Notification::fake();

        $registration = $this->post('/register', [
            'admission_number' => $student->admission_number,
            'email' => $student->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', $student->email)->firstOrFail();

        $this->assertSame('student', $user->role);
        $this->assertSame($student->id, $user->student->id);
        Notification::assertSentTo($user, VerifyEmail::class);
        $registration->assertRedirect(route('dashboard', absolute: false));

        $this->post('/logout');
        $this->post('/login', ['email' => $student->email, 'password' => 'password123'])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);

        $this->get(route('bookings.create'))->assertRedirect(route('verification.notice'));

        $user->markEmailAsVerified();
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Book a hostel')
            ->assertSee('Bookings')
            ->assertDontSee(route('students.index'));

        $this->actingAs($user)
            ->get(route('bookings.create'))
            ->assertOk()
            ->assertSee($student->fullName())
            ->assertSee($hostel->booking_period_name)
            ->assertDontSee('name="student_id"', false)
            ->assertDontSee('name="requested_check_in_date"', false)
            ->assertDontSee('name="requested_check_out_date"', false);

        $this->actingAs($user)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
            ])
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'student_id' => $student->id,
            'room_id' => $room->id,
            'preferred_hostel_id' => $hostel->id,
            'status' => 'pending',
            'processed_by' => null,
        ]);
        $booking = Booking::where('student_id', $student->id)->firstOrFail();
        $this->assertSame('2026-11-01', $booking->requested_check_in_date->toDateString());
        $this->assertSame('2026-11-08', $booking->requested_check_out_date->toDateString());
    }

    public function test_registration_requires_an_unlinked_active_student_profile(): void
    {
        Notification::fake();

        $this->post('/register', [
            'admission_number' => 'UNKNOWN-001',
            'email' => 'unknown@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('admission_number');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_student_can_only_see_and_open_their_own_bookings(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $otherStudent = $this->createStudent('Grace', 'grace@example.com', 'ADM-002');
        $user = $this->createStudentUser($student);
        $ownBooking = $this->createBooking($student);
        $otherBooking = $this->createBooking($otherStudent);

        $this->actingAs($user)
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('James')
            ->assertDontSee('Grace');

        $this->get(route('bookings.show', $otherBooking))->assertNotFound();
        $this->put(route('bookings.update', $ownBooking), [
            'status' => 'approved',
        ])->assertForbidden();

        $this->assertDatabaseHas('bookings', [
            'id' => $ownBooking->id,
            'status' => 'pending',
        ]);
    }

    public function test_student_cannot_submit_another_students_id_when_booking(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $otherStudent = $this->createStudent('Grace', 'grace@example.com', 'ADM-002');
        $user = $this->createStudentUser($student);
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);

        $this->actingAs($user)
            ->post(route('bookings.store'), [
                'student_id' => $otherStudent->id,
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
            ])
            ->assertSessionHasErrors('student_id');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_staff_can_still_create_a_booking_for_a_student(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $staff = User::factory()->create(['role' => 'hostel_manager']);
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);

        $this->actingAs($staff)
            ->post(route('bookings.store'), [
                'student_id' => $student->id,
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
            ])
            ->assertRedirect(route('bookings.index'));

        $this->assertDatabaseHas('bookings', [
            'student_id' => $student->id,
            'processed_by' => $staff->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_two_students_can_book_two_beds_but_a_third_cannot(): void
    {
        $firstStudent = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $secondStudent = $this->createStudent('Alex', 'alex@example.com', 'ADM-003');
        $thirdStudent = $this->createStudent('Taylor', 'taylor@example.com', 'ADM-004');
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);
        $firstUser = $this->createStudentUser($firstStudent);
        $secondUser = $this->createStudentUser($secondStudent);
        $thirdUser = $this->createStudentUser($thirdStudent);

        foreach ([$firstUser, $secondUser] as $user) {
            $this->actingAs($user)
                ->post(route('bookings.store'), [
                    'room_id' => $room->id,
                    'preferred_hostel_id' => $hostel->id,
                ])
                ->assertRedirect(route('bookings.index'));
        }

        $this->actingAs($thirdUser)
            ->getJson(route('bookings.available-rooms', ['preferred_hostel_id' => $hostel->id]))
            ->assertOk()
            ->assertExactJson([]);

        $this->actingAs($thirdUser)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
            ])
            ->assertSessionHasErrors('room_id');

        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_students_cannot_override_the_manager_configured_period(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);
        $user = $this->createStudentUser($student);

        $this->actingAs($user)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
                'requested_check_in_date' => '2026-12-01',
                'requested_check_out_date' => '2026-12-20',
            ])
            ->assertSessionHasErrors(['requested_check_in_date', 'requested_check_out_date']);

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_allocating_a_reserved_booking_uses_its_existing_room_hold(): void
    {
        $student = $this->createStudent('James', 'james@example.com', 'ADM-001');
        $hostel = $this->createHostel();
        $room = $this->createRoom($hostel);
        $user = $this->createStudentUser($student);
        $manager = User::factory()->create(['role' => 'hostel_manager']);

        $this->actingAs($user)
            ->post(route('bookings.store'), [
                'room_id' => $room->id,
                'preferred_hostel_id' => $hostel->id,
            ])
            ->assertRedirect(route('bookings.index'));

        $booking = Booking::firstOrFail();

        $this->actingAs($manager)
            ->post(route('allocations.store'), [
                'student_id' => $student->id,
                'room_id' => $room->id,
                'allocation_date' => '2026-10-17',
                'check_in_date' => '2026-11-01',
                'expected_check_out_date' => '2026-11-08',
            ])
            ->assertRedirect(route('allocations.index'));

        $this->assertDatabaseHas('allocations', [
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'approved',
        ]);
        $this->assertSame(1, $room->fresh()->availableSpacesForPeriod('2026-11-01', '2026-11-08'));
    }

    public function test_students_can_only_reserve_rooms_in_matching_or_mixed_hostels(): void
    {
        $student = $this->createStudent('Grace', 'grace@example.com', 'ADM-002', 'female');
        $user = $this->createStudentUser($student);
        $maleHostel = $this->createHostel();
        $maleRoom = $this->createRoom($maleHostel);
        $femaleHostel = Hostel::create([
            'hostel_name' => 'Pearl Residence',
            'hostel_code' => 'PRL-02',
            'location' => 'North wing',
            'booking_period_name' => '2026 Term 2',
            'booking_period_start' => '2026-11-01',
            'booking_period_end' => '2026-11-08',
            'period_fee' => 12000,
            'gender_category' => 'female',
            'status' => 'active',
        ]);
        $femaleRoom = $this->createRoom($femaleHostel, 'F1');

        $this->actingAs($user)
            ->post(route('bookings.store'), [
                'room_id' => $maleRoom->id,
                'preferred_hostel_id' => $maleHostel->id,
            ])
            ->assertSessionHasErrors('room_id');

        $availableRooms = $this->getJson(route('bookings.available-rooms', [
            'preferred_hostel_id' => $femaleHostel->id,
        ]))->assertOk();

        $availableRooms->assertJsonCount(1)->assertJsonFragment(['id' => $femaleRoom->id]);
        $this->assertDatabaseCount('bookings', 0);
    }

    private function createStudent(string $firstName, string $email, string $admissionNumber, string $gender = 'male'): Student
    {
        return Student::create([
            'admission_number' => $admissionNumber,
            'first_name' => $firstName,
            'last_name' => 'Test',
            'gender' => $gender,
            'phone' => '0700000000',
            'email' => $email,
            'course' => 'Computer Science',
            'academic_year' => '2026',
            'status' => 'active',
        ]);
    }

    private function createStudentUser(Student $student): User
    {
        $user = User::factory()->create([
            'name' => $student->fullName(),
            'email' => $student->email,
            'role' => 'student',
        ]);

        $student->user()->associate($user)->save();

        return $user;
    }

    private function createHostel(): Hostel
    {
        return Hostel::create([
            'hostel_name' => 'Apex Hostel',
            'hostel_code' => 'APX-01',
            'location' => 'Main campus',
            'booking_period_name' => '2026 Term 2',
            'booking_period_start' => '2026-11-01',
            'booking_period_end' => '2026-11-08',
            'period_fee' => 12000,
            'gender_category' => 'male',
            'status' => 'active',
        ]);
    }

    private function createRoom(Hostel $hostel, string $roomNumber = 'A1'): Room
    {
        return Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => $roomNumber,
            'capacity' => 2,
            'room_type' => 'single',
            'period_fee' => $hostel->period_fee,
            'status' => 'available',
        ]);
    }

    private function createBooking(Student $student): Booking
    {
        return Booking::create([
            'student_id' => $student->id,
            'requested_check_in_date' => '2026-11-01',
            'requested_check_out_date' => '2026-11-08',
            'booking_date' => '2026-10-16',
            'status' => 'pending',
        ]);
    }
}
