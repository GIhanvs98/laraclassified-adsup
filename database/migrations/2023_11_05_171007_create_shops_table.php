<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->comment('User id');
            $table->text('wallpaper')->comment('Shop wallpaper location');
            $table->json('open_hours')->default(new Expression('(JSON_ARRAY())'));
            $table->string('title', 150)->comment('Title of the shop'); // Normally taken default from username. then can change as the request from customer in admin panel.
            $table->string('description', 500)->comment('Description of the shop');
            $table->string('address', 500)->comment('Shop Address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
