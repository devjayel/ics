<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resident_unit_leaders', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('resident_unit_leaders', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};
