<?php

use App\Models\Post;
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
        Schema::create('post_reviewing_violations', function (Blueprint $table) { # This table only contains review violation messages and the last date for edit only.
            $table->id();
            $table->foreignIdFor(Post::class);
            $table->string('reason', 5000);
            $table->dateTime('last_datetime')->nullable()->comment('Remaining days to complete revision and resubmition before auto deletion.');
            $table->dateTime('rechecked_datetime')->nullable()->comment('Date and time when user rechecked/updated the ad post.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_reviewing_violations');
    }
};
