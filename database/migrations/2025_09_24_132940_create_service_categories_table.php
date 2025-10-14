<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();  // e.g. 'tattoo', 'piercing', 'laser'
            $table->string('name');           // Display name
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('service_categories');
    }
};
