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
        Schema::create('report_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class);
            $table->enum('reason', ['sold-out-or-unavailable', 'fraud', 'duplicate', 'spam', 'wrong-category', 'offensive', 'other']);
            $table->string('email');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_ads');
    }
};
