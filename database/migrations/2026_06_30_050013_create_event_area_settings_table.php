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
        Schema::create('event_area_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accreditation_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->boolean('is_avp_hidden')->default(false);
            $table->timestamps();

            // Ensure only one setting per event + area
            $table->unique(['accreditation_event_id', 'area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_area_settings');
    }
};
