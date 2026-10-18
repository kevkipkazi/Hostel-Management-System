<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('preferred_hostel_id')->nullable()->constrained('hostels')->nullOnDelete();
            $table->date('requested_check_in_date');
            $table->date('requested_check_out_date')->nullable();
            $table->date('booking_date');
            $table->string('status')->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
