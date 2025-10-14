<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Tattoo", "Ear Piercing", "Laser Removal"
            $table->enum('category', ['tattoo','ear','laser'])->index();
            $table->unsignedBigInteger('base_price')->default(0); // in minor units (pence)
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('services');
    }
};
