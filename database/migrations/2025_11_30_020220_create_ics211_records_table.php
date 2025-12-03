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
        Schema::create('ics211_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('rul_id')->constrained('resident_unit_leaders')->onDelete('cascade');
            $table->string('name');
            $table->date('start_date');
            $table->time('start_time');
            $table->string('checkin_location');
            $table->text('remarks')->nullable();
            $table->string('status')->default('pending'); // completed, pending, ongoing
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ics211_records');
    }
};
