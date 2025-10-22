<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		Schema::create('employee_expenses', function (Blueprint $table) {
			$table->id();
			$table->datetime('trans_date');
			$table->bigInteger('employee_id')->unsigned();
			$table->string('bill_no', 191)->nullable();
			$table->unsignedBigInteger('expense_category_id');
			$table->decimal('amount', 28, 8);
			$table->text('description')->nullable();
			$table->string('attachment', 191)->nullable();
			$table->tinyInteger('status')->default(0);
			$table->bigInteger('created_user_id')->nullable();
			$table->bigInteger('updated_user_id')->nullable();
			$table->timestamps();

			$table->foreign('expense_category_id')->references('id')->on('employee_expense_categories')->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		Schema::dropIfExists('employee_expenses');
	}
};
