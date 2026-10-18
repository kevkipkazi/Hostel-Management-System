<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('rooms')->update([
            'capacity' => 2,
            'room_type' => 'double',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing room sizes are normalized to two occupants and cannot be safely inferred on rollback.
    }
};
