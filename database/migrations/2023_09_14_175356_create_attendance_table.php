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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->date('date');
            $table->tinyInteger('status')->comment('0 = Absent | 1 = Present | 2 = Leave');
            $table->string('leave_type', 50)->nullable();
            $table->string('leave_duration', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
