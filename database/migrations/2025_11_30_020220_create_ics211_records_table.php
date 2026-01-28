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
            $table->string('token')->unique();
            $table->foreignId('rul_id')->constrained('resident_unit_leaders')->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->date('start_date');
            $table->time('start_time');
            $table->string('checkin_location');
            $table->string('start_coordinates')->nullable();
            $table->string('end_coordinates')->nullable();
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamp('end_timestamp')->nullable();
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
