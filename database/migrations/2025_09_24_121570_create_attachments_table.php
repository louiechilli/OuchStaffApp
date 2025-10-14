<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();

            $table->morphs('attachable'); // attachable_type, attachable_id

            $table->string('disk', 20)->default('s3');
            $table->string('bucket')->nullable();
            $table->string('path'); // S3 key (e.g., artists/12/bookings/345/reference/uuid.jpg)

            $table->string('original_name')->nullable();
            $table->string('mime_type', 191)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('checksum', 191)->nullable();

            $table->enum('kind', ['reference_image','healed_photo','consent_form','aftercare_doc','other'])
                ->default('other')->index();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attachments');
    }
};
