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
        Schema::table('subadmin2', function (Blueprint $table) {
            $table->unsignedInteger('district_id_city')->default(null)->comment('District id with respect to the city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subadmin2', function(Blueprint $table) {
            $table->dropColumn('district_id_city');
        });
    }
};
