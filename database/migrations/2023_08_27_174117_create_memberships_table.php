<?php

use App\Models\Currency;
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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Name of the membership');
            $table->text('description', 500)->comment('Description of the membership');
            $table->text('icon')->comment('svg or png icon of the membership');
            $table->integer('allowed_ads')->default(null)->comment('Number of allowed ads per membership per category ( null = infinite )')->nullable();
            $table->integer('allowed_pictures')->default(null)->comment('Number of allowed pictures per ad ( null = infinite )')->nullable();
            $table->boolean('doorstep_delivery')->default(false)->comment('Whether door step delivery avaliable or not (true/false)');
            $table->double('amount', 10, 2)->default(0.00)->comment('Whether door step delivery avaliable or not (true/false)');
            $table->foreignIdFor(Currency::class)->default('LKR')->comment('Currency type');
			$table->boolean('active')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
