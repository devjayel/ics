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
        Schema::create('ics_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('ics211_record_id')->constrained('ics211_records')->onDelete('cascade');
            $table->foreignId('rul_id')->constrained('resident_unit_leaders')->onDelete('cascade');
            $table->string('action'); // created, updated, personnel_added, personnel_removed, status_changed, etc.
            $table->text('description');
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->timestamps();
            
            $table->index('ics211_record_id');
            $table->index('rul_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ics_logs');
    }
};
