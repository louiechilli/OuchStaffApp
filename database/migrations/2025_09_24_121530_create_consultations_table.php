<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('consultations', function (Blueprint $table) {
            $table->foreignId('booking_id')->primary()->constrained('bookings')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('notes_internal')->nullable();
            $table->string('payment_link_url')->nullable();       // e.g. Stripe payment link
            $table->string('external_contact_link')->nullable();  // CRM/contact URL, optional
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('consultations');
    }
};
