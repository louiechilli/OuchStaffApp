<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->unsignedBigInteger('amount');       // minor units
            $table->string('currency', 3)->default('GBP');

            $table->enum('purpose', ['deposit','final','other'])->default('other');
            $table->enum('method', ['cash','stripe','card_reader']);
            $table->enum('status', ['pending','authorized','succeeded','failed','refunded','cancelled'])
                ->default('pending')->index();

            $table->enum('provider', ['none','stripe','zettle','sumup','square','other'])->default('none');
            $table->string('provider_reference')->nullable(); // PaymentIntent id, reader txn id, etc.
            $table->json('raw_payload')->nullable();

            $table->dateTime('captured_at')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
