<?php

use App\Models\Picture;
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
        Schema::table('thumbnail_images', function (Blueprint $table) {
            $table->foreignIdFor(Picture::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thumbnail_images', function (Blueprint $table) {
            $table->dropForeignIdFor(Picture::class);
        });
    }
};
