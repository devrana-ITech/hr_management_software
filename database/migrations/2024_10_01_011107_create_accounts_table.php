<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id');
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('slug', 100)->nullable();
            $table->tinyInteger('is_bank')->default(0);
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('accounts');
    }
};
