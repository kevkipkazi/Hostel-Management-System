<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::query();
        $hostels = Hostel::query();
        $rooms = Room::query();
        $bookings = Booking::query();
        $allocations = Allocation::query();
        $payments = Payment::query();

        if ($request->filled('from_date')) {
            $students->whereDate('created_at', '>=', $request->from_date);
            $hostels->whereDate('created_at', '>=', $request->from_date);
            $rooms->whereDate('created_at', '>=', $request->from_date);
            $bookings->whereDate('booking_date', '>=', $request->from_date);
            $allocations->whereDate('allocation_date', '>=', $request->from_date);
            $payments->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $students->whereDate('created_at', '<=', $request->to_date);
            $hostels->whereDate('created_at', '<=', $request->to_date);
            $rooms->whereDate('created_at', '<=', $request->to_date);
            $bookings->whereDate('booking_date', '<=', $request->to_date);
            $allocations->whereDate('allocation_date', '<=', $request->to_date);
            $payments->whereDate('payment_date', '<=', $request->to_date);
        }

        return view('reports.index', [
            'studentCount' => $students->count(),
            'hostelCount' => $hostels->count(),
            'roomCount' => $rooms->count(),
            'bookingCount' => $bookings->count(),
            'allocationCount' => $allocations->count(),
            'paymentCount' => $payments->count(),
            'paymentTotal' => $payments->sum('amount_paid'),
            'students' => Student::latest()->take(10)->get(),
            'roomsList' => Room::with('hostel')->take(10)->get(),
            'paymentsList' => Payment::with('student')->latest()->take(10)->get(),
        ]);
    }

    public function students()
    {
        $students = Student::latest()->get();

        return view('reports.students', compact('students'));
    }

    public function rooms()
    {
        $rooms = Room::with('hostel')->get();

        return view('reports.rooms', compact('rooms'));
    }

    public function payments()
    {
        $payments = Payment::with(['student', 'allocation.room'])->latest()->get();

        return view('reports.payments', compact('payments'));
    }

    public function outstandingFees()
    {
        $students = Student::with('allocations.room', 'payments')->get();

        return view('reports.outstanding-fees', compact('students'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(['students', 'payments'])],
        ]);
        $type = $validated['type'] ?? 'payments';

        abort_unless(
            $type === 'students'
                ? $request->user()->can('manage-hostels')
                : $request->user()->can('manage-payments'),
            403
        );

        if ($type === 'students') {
            $rows = Student::all()->map(fn ($s) => [
                'admission_number' => $s->admission_number,
                'name' => $s->fullName(),
                'course' => $s->course,
                'status' => $s->status,
            ]);
        } else {
            $rows = Payment::with('student')->get()->map(fn ($p) => [
                'receipt_number' => $p->receipt_number,
                'student' => $p->student?->fullName(),
                'amount_paid' => $p->amount_paid,
                'payment_date' => $p->payment_date,
            ]);
        }

        $filename = $type.'_report_'.now()->format('Ymd').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $fh = fopen('php://output', 'w');
            $first = $rows->first();
            if ($first) {
                fputcsv($fh, array_keys($first->toArray()));
            }
            foreach ($rows as $row) {
                fputcsv($fh, $row->toArray());
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
