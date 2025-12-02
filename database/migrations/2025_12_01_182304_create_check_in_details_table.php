<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('check_in_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('ics211_record_id')->constrained('ics211_records')->onDelete('cascade');
            $table->string('order_request_number');
            $table->date('checkin_date');
            $table->time('checkin_time');
            $table->string('kind');
            $table->string('category'); // e.g., Personnel, Equipment
            $table->string('type');
            $table->string('resource_identifier');
            $table->string('name_of_leader');
            $table->string('contact_information');
            $table->integer('quantity'); // number of personnel or items
            $table->string('department');
            $table->string('departure_point_of_origin');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('departure_method_of_travel');
            $table->boolean('with_manifest')->default(false);
            $table->string('incident_assignment')->nullable();
            $table->string('other_qualifications')->nullable();
            $table->boolean('sent_resl')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_details');
    }
};
