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
        Schema::create('cash_register_history', function (Blueprint $table) {
            $table->id();
            $table->string('register_id', 50);
            $table->dateTime('opening_date');
            $table->dateTime('closing_date');
            $table->decimal('available_kilos', 10, 2)->default(0);
            $table->decimal('invested_value', 12, 2)->default(0);
            $table->decimal('recovered_balance', 12, 2)->default(0);
            $table->decimal('profits', 12, 2)->default(0);
            $table->decimal('profit_margin', 10, 2)->default(0);
            $table->decimal('kilos_sold', 10, 2)->default(0);
            $table->decimal('kilos_purchased', 10, 2)->default(0);
            $table->timestamps();

            $table->index('register_id');
            $table->index('closing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_history');
    }
};

