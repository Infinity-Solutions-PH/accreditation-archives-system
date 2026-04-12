<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accreditation_events', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title')->unique();
        });

        // Populate slugs for existing records
        $events = DB::table('accreditation_events')->get();
        foreach ($events as $event) {
            $slug = Str::slug($event->title);
            // Check for collision
            $count = DB::table('accreditation_events')->where('slug', $slug)->count();
            if ($count > 0) {
                $slug = $slug . '-' . $event->id;
            }
            DB::table('accreditation_events')->where('id', $event->id)->update(['slug' => $slug]);
        }

        // Make slug non-nullable after population
        Schema::table('accreditation_events', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accreditation_events', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
