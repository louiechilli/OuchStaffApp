<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('booking_type_id')->nullable()->after('type')
                ->constrained('booking_types')->nullOnDelete();
        });

        // Backfill booking_type_id from existing bookings.type if possible
        if (Schema::hasColumn('bookings', 'type')) {
            $types = DB::table('booking_types')->pluck('id','key'); // ['consultation'=>1, 'session'=>2, ...]
            $bookings = DB::table('bookings')->select('id','type')->get();
            foreach ($bookings as $b) {
                if ($b->type && isset($types[$b->type])) {
                    DB::table('bookings')->where('id',$b->id)->update(['booking_type_id' => $types[$b->type]]);
                }
            }
        }

        // Optional: later you can drop the enum column 'type' once fully migrated
        // Schema::table('bookings', function (Blueprint $table) { $table->dropColumn('type'); });
    }

    public function down(): void {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('booking_type_id');
        });
    }
};
