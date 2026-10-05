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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->year('year');
            $table->enum('leave_type', ['sick', 'casual', 'earned', 'maternity', 'paternity', 'bereavement']);
            $table->unsignedInteger('allocated_days')->default(0);
            $table->unsignedInteger('used_days')->default(0);
            $table->timestamps();
            $table->unique(['employee_id', 'year', 'leave_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
