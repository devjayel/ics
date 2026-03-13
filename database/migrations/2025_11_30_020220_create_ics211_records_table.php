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
            $table->string('name');
            $table->string('type');
            $table->string('order_request_number');
            $table->string('checkin_location');
            $table->string('region')->nullable();
            $table->text('remarks')->nullable();
            $table->string('remarks_image_attachment')->nullable();
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
