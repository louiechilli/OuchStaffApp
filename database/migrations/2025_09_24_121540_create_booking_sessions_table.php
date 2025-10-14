<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_sessions', function (Blueprint $table) {
            $table->foreignId('booking_id')->primary()->constrained('bookings')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('reference_notes')->nullable(); // references / design notes
            $table->unsignedBigInteger('quoted_total_amount')->nullable(); // snapshot of quote
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_sessions');
    }
};
