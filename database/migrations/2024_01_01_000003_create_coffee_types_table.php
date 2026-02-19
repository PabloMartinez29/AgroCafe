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
        Schema::create('coffee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('variety', ['arabica', 'robusta']);
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->enum('quality', ['premium', 'special', 'commercial']);
            $table->enum('processing_type', ['normal', 'wet', 'dry', 'pasilla'])->default('normal');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffee_types');
    }
};

