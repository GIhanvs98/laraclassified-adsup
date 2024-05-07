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
        Schema::table('memberships', function (Blueprint $table) {
            $table->boolean('post_untill_membership_duration')->default(false)->comment('This allows posts with memberships to have unlimited duration (without auto deletion in particular time like 30 days. managed by `description` filed) until the a user have a valid membership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('post_untill_membership_duration');
        });
    }
};
