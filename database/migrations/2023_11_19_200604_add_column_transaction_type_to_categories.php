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

            $table->enum('transaction_type', ['sell', 'rent', 'both'])->default('sell')->comment('This will define the transaction type of the item or the service.');

            /*
            /   This will define the transaction type of the item or the service.
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            $table->dropColumn('transaction_type');
        });
    }
};
