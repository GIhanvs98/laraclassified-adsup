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
            $table->integer('order')->default(0)->comment('Order of arrangement of districts. Use to sort results.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subadmin2', function (Blueprint $table) {
            $table->dropColumn(['order']);
        });
    }
};
