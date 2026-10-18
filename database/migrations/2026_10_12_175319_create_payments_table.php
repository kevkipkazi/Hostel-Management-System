<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('allocation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_number')->unique();
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_reference')->nullable();
            $table->string('payment_period')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
