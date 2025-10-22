<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->date('date');
            $table->time('clock_in');
            $table->time('clock_out')->nullable();
            $table->decimal('work_hour', 8, 2)->nullable();
            $table->decimal('hour_deduct', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('working_hours');
    }
};
