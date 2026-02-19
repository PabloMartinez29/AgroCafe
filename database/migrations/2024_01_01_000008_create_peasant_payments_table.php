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
        Schema::create('peasant_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['transfer', 'cash', 'check']);
            $table->string('reference', 100)->nullable();
            $table->date('payment_date');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamps();

            $table->index('purchase_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peasant_payments');
    }
};

