<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_services', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();

            $table->unsignedBigInteger('unit_price'); // minor units
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('line_total'); // unit_price * qty

            $table->primary(['booking_id','service_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_services');
    }
};
