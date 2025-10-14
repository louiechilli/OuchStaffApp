<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('booking_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // e.g. 'consultation', 'session'
            $table->string('name');            // Display name
            $table->string('icon')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('booking_types');
    }
};
