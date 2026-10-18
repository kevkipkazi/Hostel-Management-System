<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hostels', function (Blueprint $table): void {
            $table->renameColumn('monthly_fee', 'period_fee');
            $table->string('booking_period_name')->nullable()->after('period_fee');
            $table->date('booking_period_start')->nullable()->after('booking_period_name');
            $table->date('booking_period_end')->nullable()->after('booking_period_start');
        });

        Schema::table('rooms', function (Blueprint $table): void {
            $table->renameColumn('monthly_fee', 'period_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table): void {
            $table->renameColumn('period_fee', 'monthly_fee');
        });

        Schema::table('hostels', function (Blueprint $table): void {
            $table->dropColumn(['booking_period_name', 'booking_period_start', 'booking_period_end']);
            $table->renameColumn('period_fee', 'monthly_fee');
        });
    }
};
