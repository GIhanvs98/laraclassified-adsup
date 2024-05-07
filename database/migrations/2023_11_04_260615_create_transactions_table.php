<?php

use App\Models\Currency;
use App\Models\Membership;
use App\Models\Package;
use App\Models\Post;
use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable(); 
			$table->string('name', 100);
			$table->string('email', 100);
			$table->string('phone', 60); // +94XXXXXXXXX format.
            $table->enum('payment_type', ['membership', 'ad-promotion']);
            $table->foreignIdFor(Membership::class)->nullable();
            $table->foreignIdFor(Post::class)->nullable(); // Ad promotion combination.
            $table->foreignIdFor(Package::class)->nullable(); // Ad promotion combination.
            $table->text('ipg_transaction_id');
            $table->text('response_transaction_id');
            $table->enum('payment_status', ['pending', 'success', 'declined', 'canceled']);
            $table->foreignIdFor(Currency::class)->default('LKR')->comment('Currency type');
            $table->string('gross_amount')->nullable();
            $table->string('discount')->nullable();
            $table->string('handling_fee')->nullable();
            $table->string('net_amount')->nullable();
            $table->string('payment_method')->default('onepay.lk');
            $table->string('reference_id', 18)->comment('Unique reference number for each transaction (Length must be within 10 - 20)');
            $table->dateTime('payment_started_datetime')->comment('Payment is 1st started this date-time.');
            $table->dateTime('payment_valid_untill_datetime')->nullable()->comment('Payment is valid untill this date-time');
            $table->dateTime('payment_due_datetime')->nullable()->comment('Payment should be given before this date-time. Or else the membership or ad-promotions will be canceled.');
            $table->boolean('active')->default(0)->comment('States whether the payment is for recent, currently working membership or ad-promotion. 1:active, 0:expired');
            //$table->string('is_redirected')->default(0)->comment('States whether the redirection after the payment is completed. if already done once put 1 and next time show 404. 1:active, 0:expired');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};