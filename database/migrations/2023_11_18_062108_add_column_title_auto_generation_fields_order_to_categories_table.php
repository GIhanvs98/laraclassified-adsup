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
        Schema::table('categories', function (Blueprint $table) {

            $table->string('title_auto_generation_fields_order')->nullable()->comment('Seperate by `<>` to seperate the field id`s Eg:- <1> <2> <3> (<5>) (<brand> <model> (<year>))');

            /*
            /   Seperate by `<>` to seperate the field id`s. If the column is empty then show the title. If not it will be hidden.
            /
            /   <brand> <model> <model_year> (<condition>)
            /
            /   <1> <2> <3> (<5>)
            / 
            /   Use `/\<[0-9]*\>/gim` reqular expression to extract id`s. This will comes with the brackets.
            /
            /   Then remove the `<` and `>` characters from each string to extract the field id.
            */

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            $table->dropColumn('title_auto_generation_fields_order');

        });
    }
};
