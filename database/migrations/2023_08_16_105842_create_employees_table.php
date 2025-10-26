<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('employee_id', 100);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('fathers_name', 100)->nullable();
            $table->string('mothers_name', 100)->nullable();
            $table->date('date_of_birth');
            $table->string('email', 191)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('city', 191)->nullable();
            $table->string('state', 191)->nullable();
            $table->string('zip', 191)->nullable();
            $table->string('country', 191)->nullable();
            $table->string('image', 191)->nullable();

            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->bigInteger('designation_id')->unsigned()->nullable();
            $table->bigInteger('unit_id')->unsigned()->nullable();
            $table->date('joining_date');
            $table->date('end_date')->nullable();

            $table->string('salary_type', 20)->default('fixed')->comment('fixed | hourly');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('full_day_absence_fine', 10, 2)->default(0);
            $table->decimal('half_day_absence_fine', 10, 2)->default(0);
            $table->integer('yearly_leave_limit')->default(0);

            $table->string('bank_name', 191)->nullable();
            $table->string('branch_name', 191)->nullable();
            $table->string('account_name', 191)->nullable();
            $table->string('account_number', 30)->nullable();
            $table->string('swift_code', 50)->nullable();

            $table->text('remarks')->nullable();
            $table->text('custom_fields')->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->restrictOnDelete();
            $table->foreign('designation_id')->references('id')->on('designations')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('employees');
    }
};
