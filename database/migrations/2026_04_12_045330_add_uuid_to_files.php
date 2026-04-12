<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id')->index();
        });

        // Backfill existing files
        DB::table('files')->whereNull('uuid')->get()->each(function ($file) {
            DB::table('files')
                ->where('id', $file->id)
                ->update(['uuid' => Str::uuid()->toString()]);
        });

        Schema::table('files', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
