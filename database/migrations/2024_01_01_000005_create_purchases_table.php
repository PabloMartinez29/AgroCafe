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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peasant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('coffee_type_id')->constrained('coffee_types')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total', 12, 2)->virtualAs('quantity * price_per_kg');
            $table->date('purchase_date');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('peasant_id');
            $table->index('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

