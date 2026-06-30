<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('subfolder')->nullable()->after('area_id');
            $table->string('parameter')->nullable()->after('subfolder');
        });

        Schema::table('accreditation_event_files', function (Blueprint $table) {
            $table->string('subfolder')->nullable()->after('area_id');
            $table->string('parameter')->nullable()->after('subfolder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['subfolder', 'parameter']);
        });

        Schema::table('accreditation_event_files', function (Blueprint $table) {
            $table->dropColumn(['subfolder', 'parameter']);
        });
    }
};
