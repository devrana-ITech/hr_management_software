<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('employee_loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('minimum_amount', 15, 2);
            $table->decimal('maximum_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->enum('interest_type', ['fixed', 'declining'])->default('fixed');
            $table->integer('term');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('employee_loan_types');
    }
};
