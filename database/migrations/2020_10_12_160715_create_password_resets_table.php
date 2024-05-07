<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordresetsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('password_resets', function (Blueprint $table) {
			$table->string('email', 191)->nullable();
			$table->string('phone', 191)->nullable();
			$table->string('phone_country', 2)->nullable();
			$table->string('token', 191)->nullable();
			$table->timestamp('created_at')->nullable();
			$table->index(["email"]);
			$table->index(["phone"]);
			$table->index(["token"]);
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('password_resets');
	}
}
