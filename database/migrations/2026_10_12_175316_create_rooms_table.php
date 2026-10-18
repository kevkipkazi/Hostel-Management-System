<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('room_number');
            $table->integer('floor_number')->nullable();
            $table->integer('capacity');
            $table->string('room_type');
            $table->decimal('monthly_fee', 12, 2)->default(0);
            $table->string('status')->default('available');
            $table->timestamps();

            $table->unique(['hostel_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
