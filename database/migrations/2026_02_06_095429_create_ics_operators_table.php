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
        Schema::create('ics_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rul_id')->constrained('resident_unit_leaders')->onDelete('cascade');
            $table->foreignId('ics_id')->constrained('ics211_records')->onDelete('cascade');
            $table->unique(['rul_id', 'ics_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ics_operators');
    }
};
