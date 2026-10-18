<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = strtolower($user?->role ?? 'student');
        $roleLabel = match ($role) {
            'administrator' => 'Administrator',
            'hostel_manager' => 'Hostel Manager',
            'finance_officer' => 'Finance Officer',
            default => 'Student',
        };
        $canManageHostels = $user?->can('manage-hostels') ?? false;
        $canManagePayments = $user?->can('manage-payments') ?? false;

        $students = $canManageHostels ? Student::count() : 0;
        $hostels = $canManageHostels ? Hostel::count() : 0;
        $rooms = $canManageHostels ? Room::count() : 0;

        $totalAvailableSpaces = $canManageHostels
            ? Room::query()->with('hostel')->get()->sum(fn ($room) => $room->availableSpaces())
            : 0;
        $occupiedRooms = $canManageHostels
            ? Room::query()->with('hostel')->get()->sum(fn ($room) => max(0, $room->capacity - $room->availableSpaces()))
            : 0;

        $pendingBookings = $canManageHostels ? Booking::where('status', 'pending')->count() : 0;
        $activeAllocations = Allocation::where('status', 'active')->count();

        $paymentsReceived = $canManagePayments ? (Payment::sum('amount_paid') ?? 0) : 0;
        $paymentCount = $canManagePayments ? Payment::count() : 0;
        $activeAllocationPeriodFees = $canManagePayments
            ? Allocation::where('status', 'active')->with('room')->get()->sum(fn ($allocation) => (float) ($allocation->room?->period_fee ?? 0))
            : 0;
        $outstandingFees = $canManagePayments
            ? max(0, (float) $activeAllocationPeriodFees - (float) $paymentsReceived)
            : 0;

        $recentBookings = $canManageHostels
            ? Booking::with(['student', 'preferredHostel'])->latest()->take(5)->get()
            : new Collection;
        $recentPayments = $canManagePayments
            ? Payment::with(['student', 'recorder'])->latest()->take(5)->get()
            : new Collection;

        $studentProfile = $user?->student;
        if (! $studentProfile && $user?->email) {
            $studentProfile = Student::where('email', $user->email)->first();
        }
        $studentBookings = $studentProfile ? $studentProfile->bookings()->latest()->take(4)->get() : collect();
        $currentAllocation = $studentProfile ? $studentProfile->allocations()->with('room.hostel')->latest()->first() : null;
        $lastPayment = $studentProfile ? $studentProfile->payments()->latest()->first() : null;

        return view('dashboard', compact(
            'user',
            'role',
            'roleLabel',
            'canManageHostels',
            'canManagePayments',
            'students',
            'hostels',
            'rooms',
            'totalAvailableSpaces',
            'occupiedRooms',
            'pendingBookings',
            'activeAllocations',
            'paymentsReceived',
            'paymentCount',
            'outstandingFees',
            'recentBookings',
            'recentPayments',
            'studentProfile',
            'studentBookings',
            'currentAllocation',
            'lastPayment'
        ));
    }
}
