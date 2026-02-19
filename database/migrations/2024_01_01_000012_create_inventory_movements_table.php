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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coffee_type_id')->constrained('coffee_types')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->enum('movement_type', ['adjustment', 'entry', 'exit', 'return'])->default('adjustment');
            $table->text('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('movement_date');
            $table->timestamps();

            $table->index('coffee_type_id');
            $table->index('movement_date');
            $table->index('movement_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};

