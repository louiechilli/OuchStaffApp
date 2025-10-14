<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // type is extensible: 'consultation' | 'session' (+ future)
            $table->enum('type', ['consultation','session'])->index();

            $table->enum('status', ['draft','scheduled','completed','cancelled','no_show'])
                ->default('draft')->index();

            $table->dateTime('scheduled_start_at')->nullable()->index();
            $table->dateTime('scheduled_end_at')->nullable()->index();

            $table->string('location')->nullable();

            // money in minor units (pence)
            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedBigInteger('deposit_required_amount')->nullable();

            // artist ownership/assignment
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['assigned_to', 'scheduled_start_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
