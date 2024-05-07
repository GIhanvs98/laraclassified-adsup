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
        Schema::create('category_sub_category_group', function (Blueprint $table) {
			$table->bigInteger('sub_category_group_id')->unsigned()->nullable();
			$table->bigInteger('category_id')->unsigned()->nullable();
			$table->timestamps();

			$table->index(["sub_category_group_id"]);
			$table->index(["category_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_sub_category_group');
    }
};
