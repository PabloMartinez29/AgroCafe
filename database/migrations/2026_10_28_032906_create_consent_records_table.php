<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registro inmutable de autorizaciones (Ley 1581 de 2012).
     *
     * No tiene llave foránea hacia users a propósito: la prueba de la
     * autorización debe conservarse aunque el titular elimine su cuenta.
     */
    public function up(): void
    {
        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('subject');
            $table->string('subject_name');
            $table->string('subject_email')->nullable();
            $table->string('document', 30);
            $table->string('document_version', 20);
            $table->char('document_hash', 64);
            $table->string('action', 20);
            $table->string('channel', 40);
            $table->text('consent_text');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedBigInteger('captured_by_id')->nullable();
            $table->string('captured_by_name')->nullable();
            $table->char('previous_hash', 64)->nullable();
            $table->char('record_hash', 64)->unique();
            $table->timestamp('created_at');

            $table->index(['subject_type', 'subject_id', 'document']);
            $table->index('subject_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_records');
    }
};
