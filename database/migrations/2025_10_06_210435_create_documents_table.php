<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Document templates that can be attached to services
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content'); // HTML content of the document
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_signature')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table: which services require which documents
        Schema::create('service_document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_template_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['service_id', 'document_template_id'], 'service_document_unique');
        });

        // Signed documents for specific bookings
        Schema::create('booking_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->longText('content'); // Snapshot of document at time of signing
            $table->string('status')->default('pending'); // pending, signed, declined
            $table->text('signature_data')->nullable(); // Base64 signature image or JSON signature data
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_documents');
        Schema::dropIfExists('service_document_templates');
        Schema::dropIfExists('document_templates');
    }
};