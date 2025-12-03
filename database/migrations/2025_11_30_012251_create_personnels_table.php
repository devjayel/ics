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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('rul_id')->constrained('resident_unit_leaders')->onDelete('cascade');
            $table->string('name');
            $table->string('contact_number');
            $table->string('serial_number')->unique();
            $table->string('department');
            $table->string('fcm_token')->nullable();
            $table->string('token')->nullable()->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
