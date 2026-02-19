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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('register_name', 100);
            $table->decimal('initial_amount', 10, 2)->default(0);
            $table->decimal('base_salary', 12, 2)->default(0);
            $table->decimal('available_balance', 12, 2)->default(0);
            $table->dateTime('opening_date');
            $table->dateTime('closing_date')->nullable();
            $table->decimal('kilos_purchased', 10, 2)->default(0);
            $table->decimal('kilos_sold', 10, 2)->default(0);
            $table->decimal('operating_hours', 10, 2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index('status');
            $table->index('opening_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};

