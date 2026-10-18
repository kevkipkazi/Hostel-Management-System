<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query()->with(['student', 'allocation.room', 'recorder']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('receipt_number', 'like', "%{$search}%")
                ->orWhereHas('student', fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%"));
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::all();
        $allocations = Allocation::with('room')->get();

        return view('payments.create', compact('students', 'allocations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'allocation_id' => ['nullable', 'exists:allocations,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,mobile_money,other'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'payment_period' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['receipt_number'] = 'RCPT-' . strtoupper(Str::random(8));
        $validated['recorded_by'] = auth()->id();

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['student', 'allocation.room', 'recorder']);

        return view('payments.show', compact('payment'));
    }
}
