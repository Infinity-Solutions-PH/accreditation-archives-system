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
        Schema::table('google_users_info', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('accreditor_id')->nullable()->after('user_id')->constrained('accreditors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_users_info', function (Blueprint $table) {
            //
        });
    }
};
