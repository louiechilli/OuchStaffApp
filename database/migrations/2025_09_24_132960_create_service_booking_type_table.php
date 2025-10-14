<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_booking_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('booking_type_id')->constrained('booking_types')->cascadeOnDelete();

            $table->boolean('is_enabled')->default(true)->index();
            $table->unsignedInteger('default_duration_minutes')->nullable();
            $table->unsignedBigInteger('default_price')->nullable(); // minor units
            $table->boolean('requires_deposit')->default(false);
            $table->unsignedBigInteger('default_deposit')->nullable(); // minor units
            $table->json('form_schema')->nullable();

            $table->timestamps();
            $table->unique(['service_id','booking_type_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('service_booking_type');
    }
};
