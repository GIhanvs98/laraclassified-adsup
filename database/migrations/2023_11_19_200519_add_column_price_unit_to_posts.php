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
        Schema::table('posts', function (Blueprint $table) {

            $table->string('price_unit')->nullable()->comment('Only works for rents. Value picked by the categories. Keep `null` for `total`.');

            /*
            /   Only works for `Rents`. 
            /
            /   And for `Land` category.
            /
            /   This will define the `price-unit` fields in ad posting.
            /
            /   Keep `null` for `total`.
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {

            $table->dropColumn('price_unit');
        });
    }
};
