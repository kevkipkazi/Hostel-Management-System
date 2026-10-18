<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->decimal('monthly_fee', 12, 2)->default(0)->after('location');
        });

        DB::table('hostels')->orderBy('id')->get(['id'])->each(function (object $hostel): void {
            $existingAverage = DB::table('rooms')->where('hostel_id', $hostel->id)->avg('monthly_fee');

            if ($existingAverage !== null) {
                DB::table('hostels')->where('id', $hostel->id)->update(['monthly_fee' => $existingAverage]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            $table->dropColumn('monthly_fee');
        });
    }
};
