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
        Schema::create('payslip_benefits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('payslip_id')->unsigned();
            $table->string('name', 191);
            $table->decimal('amount', 28, 8);
            $table->string('amount_type', 10)->comment('fixed | percent');
            $table->string('type', 20)->deffault('add')->comment('add | deduct');
            $table->timestamps();

            $table->foreign('payslip_id')->references('id')->on('payslips')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip_benefits');
    }
};
