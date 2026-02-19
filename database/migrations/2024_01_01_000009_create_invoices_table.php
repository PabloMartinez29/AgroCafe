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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('set null');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->string('invoice_number', 20)->unique();
            $table->date('invoice_date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('taxes', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('payment_status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('due_date')->nullable();
            $table->enum('transaction_type', ['sale', 'purchase'])->default('sale');
            $table->timestamps();

            $table->index('transaction_type');
            $table->index('sale_id');
            $table->index('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

