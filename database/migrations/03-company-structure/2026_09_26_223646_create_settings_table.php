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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 50)->default('company')->unique();
            $table->string('company_name', 100);
            $table->string('company_logo')->nullable();
            $table->string('timezone')->default('Asia/Riyadh');
            $table->time('work_start_time')->default('08:00:00');
            $table->time('work_end_time')->default('17:00:00');
            $table->json('weekend_days');
            $table->decimal('late_deduction_rate', 5, 2)->default(0.5);
            $table->decimal('absent_deduction_rate', 5, 2)->default(1.0);
            $table->decimal('overtime_rate', 5, 2)->default(1.5);
            $table->unsignedInteger('annual_leave_days')->default(21);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
