<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->morphs('notable'); // notable_type, notable_id

            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->enum('visibility', ['internal','shared_with_client'])->default('internal')->index();
            $table->text('body');

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('notes');
    }
};
