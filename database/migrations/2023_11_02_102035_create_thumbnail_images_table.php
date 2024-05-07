<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Post;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('thumbnail_images', function (Blueprint $table) {
            $table->id();
			$table->foreignIdFor(Post::class);
			$table->string('filename', 255)->nullable();
			$table->string('mime_type', 200)->nullable();
			$table->boolean('active')->unsigned()->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thumbnail_images');
    }
};
