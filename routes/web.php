<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HostelController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\EnsureStudentEmailIsVerified;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('students', StudentController::class)
        ->only(['create', 'store'])
        ->middleware('can:manage-hostels');
    Route::resource('students', StudentController::class)
        ->only(['edit', 'update', 'destroy'])
        ->middleware('can:manage-hostels');
    Route::resource('students', StudentController::class)
        ->only(['index', 'show'])
        ->middleware('can:manage-hostels');
    Route::resource('hostels', HostelController::class)->middleware('can:manage-hostels');
    Route::resource('rooms', RoomController::class)->middleware('can:manage-hostels');
    Route::get('/bookings/available-rooms', [BookingController::class, 'availableRooms'])
        ->middleware(['role:administrator,hostel_manager,student', EnsureStudentEmailIsVerified::class])
        ->name('bookings.available-rooms');
    Route::resource('bookings', BookingController::class)
        ->only(['create', 'store', 'index', 'show'])
        ->middleware(['role:administrator,hostel_manager,student', EnsureStudentEmailIsVerified::class]);
    Route::resource('bookings', BookingController::class)
        ->only(['update', 'destroy'])
        ->middleware('can:manage-hostels');
    Route::resource('allocations', AllocationController::class)->middleware('can:manage-hostels');
    Route::resource('payments', PaymentController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->middleware('can:manage-payments');

    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:administrator')
        ->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])
        ->middleware('role:administrator,hostel_manager,finance_officer')
        ->name('reports.export');
    Route::get('/reports/students', [ReportController::class, 'students'])
        ->middleware('can:manage-hostels')
        ->name('reports.students');
    Route::get('/reports/rooms', [ReportController::class, 'rooms'])
        ->middleware('can:manage-hostels')
        ->name('reports.rooms');
    Route::get('/reports/payments', [ReportController::class, 'payments'])
        ->middleware('can:manage-payments')
        ->name('reports.payments');
    Route::get('/reports/outstanding-fees', [ReportController::class, 'outstandingFees'])
        ->middleware('can:manage-payments')
        ->name('reports.outstanding-fees');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
