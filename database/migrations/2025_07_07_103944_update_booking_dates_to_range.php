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
    Schema::table('bookings', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('booking_date');

        // ✅ Add new columns as nullable to prevent errors
        $table->date('booking_start_date')->nullable();
        $table->date('booking_end_date')->nullable();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
