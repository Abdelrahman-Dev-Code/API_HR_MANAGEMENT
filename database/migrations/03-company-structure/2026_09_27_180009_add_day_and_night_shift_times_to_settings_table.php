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
        Schema::table('settings', function (Blueprint $table) {
            $table->time('day_shift_start_time')->default('08:00:00');
            $table->time('day_shift_end_time')->default('17:00:00');
            $table->time('night_shift_start_time')->default('22:00:00');
            $table->time('night_shift_end_time')->default('06:00:00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'day_shift_start_time',
                'day_shift_end_time',
                'night_shift_start_time',
                'night_shift_end_time',
            ]);
        });
    }
};
