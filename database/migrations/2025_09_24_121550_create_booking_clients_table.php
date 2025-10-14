<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_clients', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('role', ['primary','secondary','guardian'])->default('primary');

            $table->primary(['booking_id','client_id']);
            $table->index('role');
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_clients');
    }
};
