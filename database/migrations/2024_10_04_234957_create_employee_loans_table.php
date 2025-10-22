<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id', 30)->nullable();
            $table->date('application_date');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('loan_type_id')->nullable();
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->enum('interest_type', ['fixed', 'declining'])->default('fixed');
            $table->decimal('monthly_installment', 15, 2);
            $table->timestamp('loan_issued_at')->nullable();
            $table->timestamp('loan_due_at')->nullable();
            $table->string('loan_purpose');
            $table->text('attachment')->nullable();
            $table->text('description')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'repaid'])->default('pending');
            $table->date('decision_date')->nullable();
            $table->bigInteger('action_user_id')->nullable();
            $table->bigInteger('created_user_id')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('loan_type_id')->references('id')->on('employee_loan_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('employee_loans');
    }
};
