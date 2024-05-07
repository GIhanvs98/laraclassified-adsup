<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Membership;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('country_code', 2)->nullable();
			$table->string('language_code', 10)->nullable();
			$table->tinyInteger('user_type_id')->unsigned()->nullable();
			$table->integer('gender_id')->unsigned()->nullable();
			$table->string('name', 100);
			$table->string('photo', 255)->nullable();
			$table->string('about', 255)->nullable();
			$table->enum('auth_field', ['email', 'phone'])->nullable()->default('email');
			$table->string('email', 100)->nullable();
			$table->string('phone', 60)->nullable();
			$table->string('phone_national', 30)->nullable();
			$table->string('phone_country', 2)->nullable();
			$table->boolean('phone_hidden')->unsigned()->nullable()->default('0');
			$table->string('phone_alternate_1', 60)->nullable()->comment('Alternate phone number'); // +94XXXXXXXXX format.
			$table->string('phone_alternate_2', 60)->nullable()->comment('Alternate phone number'); // +94XXXXXXXXX format.
			$table->string('whatsapp_number', 60)->nullable()->comment('Whatsapp number'); // +94XXXXXXXXX format.
			$table->string('username', 100)->nullable();
			$table->string('password', 60)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->boolean('is_admin')->unsigned()->nullable()->default('0');
			$table->boolean('can_be_impersonated')->unsigned()->nullable()->default('1');
			$table->boolean('disable_comments')->unsigned()->nullable()->default('0');
			$table->string('ip_addr', 50)->nullable();
			$table->string('provider', 50)->nullable()->comment('facebook, google, twitter, linkedin, ...');
			$table->string('provider_id', 50)->nullable()->comment('Provider User ID');
			$table->string('email_token', 32)->nullable();
			$table->string('phone_token', 32)->nullable();
			$table->timestamp('email_verified_at')->nullable()->useCurrentOnUpdate();
			$table->timestamp('phone_verified_at')->nullable()->useCurrentOnUpdate();
			$table->boolean('accept_terms')->nullable()->default('0');
			$table->boolean('accept_marketing_offers')->nullable()->default('0');
			$table->string('time_zone', 50)->nullable();
			$table->boolean('blocked')->unsigned()->nullable()->default('0');
			$table->boolean('closed')->unsigned()->nullable()->default('0');
			$table->datetime('last_activity')->nullable();
			$table->datetime('last_login_at')->nullable();
			$table->timestamp('deleted_at')->nullable();
			$table->foreignIdFor(Membership::class)->default(config('subscriptions.memberships.default_id'))->nullable()->comment('Membership id');
			$table->timestamps();
			$table->index(["country_code"]);
			$table->index(["user_type_id"]);
			$table->index(["gender_id"]);
			$table->index(["auth_field"]);
			$table->index(["email"]);
			$table->index(["phone"]);
			$table->index(["phone_country"]);
			$table->index(["username"]);
			$table->index(["email_verified_at"]);
			$table->index(["phone_verified_at"]);
			$table->index(["is_admin"]);
			$table->index(["can_be_impersonated"]);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}
}
