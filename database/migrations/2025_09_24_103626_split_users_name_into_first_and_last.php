<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns; nullable during transition
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Backfill: split existing `name` into first/last
        // Heuristic: everything except the last word -> first_name, last word -> last_name
        DB::table('users')->select('id', 'name')->orderBy('id')
            ->chunkById(500, function ($users) {
                foreach ($users as $u) {
                    $full = trim($u->name ?? '');
                    if ($full === '') {
                        DB::table('users')->where('id', $u->id)->update([
                            'first_name' => null,
                            'last_name'  => null,
                        ]);
                        continue;
                    }

                    $parts = preg_split('/\s+/', $full);
                    if (count($parts) === 1) {
                        $first = $parts[0];
                        $last  = null;
                    } else {
                        $last  = array_pop($parts);
                        $first = implode(' ', $parts);
                    }

                    DB::table('users')->where('id', $u->id)->update([
                        'first_name' => $first,
                        'last_name'  => $last,
                    ]);
                }
            });

        // Make them non-nullable if you want strictness (optional)
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable()->change(); // allow no last name if you prefer
        });

        // Optional: drop old `name` column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    public function down(): void
    {
        // Recreate `name` and join fields back
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // Backfill `name` from first/last
        DB::table('users')->select('id', 'first_name', 'last_name')->orderBy('id')
            ->chunkById(500, function ($users) {
                foreach ($users as $u) {
                    $full = trim(trim((string)$u->first_name).' '.trim((string)$u->last_name));
                    DB::table('users')->where('id', $u->id)->update([
                        'name' => $full !== '' ? $full : null,
                    ]);
                }
            });

        // Drop first/last columns
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }
        });
    }
};
