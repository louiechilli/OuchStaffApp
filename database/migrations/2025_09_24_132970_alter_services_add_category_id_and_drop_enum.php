<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')
                ->constrained('service_categories')->nullOnDelete();

            // If you still have an enum 'category' on services, we’ll keep it for backfill then drop.
            // $table->enum('category', [...]) existed previously — do nothing yet here.
        });

        // Backfill category_id from old enum column if it exists
        if (Schema::hasColumn('services', 'category')) {
            // Create defaults if missing
            $defaults = [
                'tattoo' => 'Tattoo',
                'ear'    => 'Ear / Piercing',
                'laser'  => 'Laser Removal',
            ];
            foreach ($defaults as $key => $name) {
                DB::table('service_categories')->updateOrInsert(['key' => $key], ['name' => $name]);
            }

            // Map existing rows
            $services = DB::table('services')->select('id','category')->get();
            foreach ($services as $svc) {
                if ($svc->category) {
                    $cat = DB::table('service_categories')->where('key',$svc->category)->first();
                    if ($cat) {
                        DB::table('services')->where('id',$svc->id)->update(['category_id' => $cat->id]);
                    }
                }
            }
        }

        Schema::table('services', function (Blueprint $table) {
            // Make category_id required going forward
            $table->foreignId('category_id')->nullable()->change();
            // Drop old enum if present
            if (Schema::hasColumn('services', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    public function down(): void {
        Schema::table('services', function (Blueprint $table) {
            // Recreate old enum (fallback) — you can change the enum set if needed
            $table->enum('category', ['tattoo','ear','laser'])->nullable()->after('name');
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
