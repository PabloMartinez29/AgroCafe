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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->nullable()->constrained('cooperatives')->onDelete('set null');
            $table->string('client_name', 150)->nullable();
            $table->foreignId('coffee_type_id')->constrained('coffee_types')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('price_per_kg', 10, 2);
            $table->decimal('total', 12, 2)->virtualAs('quantity * price_per_kg');
            $table->date('sale_date');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index('cooperative_id');
            $table->index('sale_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

