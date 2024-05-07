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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('compress_images', ['true', 'false', 'system_default'])->default('system_default')->nullable(false)->after('membership_id');
            $table->enum('watermark_images', ['true', 'false', 'system_default'])->default('system_default')->nullable(false)->after('compress_images');
            $table->enum('otp_verify', ['true', 'false', 'system_default'])->default('system_default')->nullable(false)->after('watermark_images');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['compress_images', 'watermark_images', 'otp_verify']);
        });
    }
};
