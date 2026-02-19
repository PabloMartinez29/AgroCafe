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
        Schema::create('historical_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coffee_type_id')->constrained('coffee_types')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->date('price_date');
            $table->enum('operation_type', ['purchase', 'sale']);
            $table->timestamps();

            $table->index('price_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_prices');
    }
};

