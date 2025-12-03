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
        Schema::create('check_in_detail_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('ics211_record_id')->constrained('ics211_records')->onDelete('cascade');
            $table->string('order_request_number');
            $table->text('remarks')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, rejected, ongoing, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_detail_histories');
    }
};
