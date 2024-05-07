<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $table = "transactions";

    protected $fillable = [
        'payment_type',
        'ipg_transaction_id',
        'payment_status',
        'currency_code',
        'gross_amount',
        'discount',
        'handling_fee',
        'net_amount',
        'payment_method',
        'reference_id',
        'payment_renewal_datetime',
        'payment_due_datetime',
        'payment_completed_datetime',
        'active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    // Custom Scopes ------------------------------------------------------------------------------------------------------------------

    /**
     * Get a transaction successfull transaction. (This might be expired one or not.)
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessfull(Builder $query, string $payment_type): void
    {
        $query
            ->where('payment_type', $payment_type)
            ->where('payment_status', 'success')
            ->whereNotNull('payment_started_datetime')
            ->whereNotNull('payment_valid_untill_datetime')
            ->whereNotNull('payment_due_datetime');
    }

    /**
     * Get a transaction which is still currently applid for a service(membership/ad-promotion).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValid(Builder $query, string $payment_type): void
    {
        $query
            ->successfull($payment_type)
            ->where('active', 1)
            ->where('payment_due_datetime', '>', now());
    }
    
    /**
     * Get a transaction which is still currently applid for a service(membership/ad-promotion) but doen with onepay.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnepayTransactions(Builder $query): void{
        $query->whereNot(function (Builder $query) {
            $query->where('payment_method', 'giveaway_package');
        });
    }

    /**
     * Get a transaction which is still currently applid for a service(membership/ad-promotion) but a give away one.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGiveawayTransactions(Builder $query): void{
        $query->where('payment_method', 'giveaway_package');
    }


    // Custom Static Methods ------------------------------------------------------------------------------------------------------------------

    /**
     * Deactivate ad-promotion transactions based on specified conditions.
     *
     * @return void
     */
    public static function deactivateAdPromotionTransactions()
    {
        $now = Carbon::now();

        self::successfull('ad-promotion')
            ->where('payment_due_datetime', '<=', $now) /* This will select all the old transactions which are expired or no longer valid */
            ->update(['active' => 0]);
    }

    /**
     * Deactivate membership transactions and update associated users.
     *
     * @return void
     */
    public static function deactivateMembershipTransactions()
    {
        $now = Carbon::now();

        $transactions = self::successfull('membership')
            ->where('payment_due_datetime', '<=', $now);

        foreach ($transactions->get() as $key => $transaction) {
            $transaction->user()->update(['membership_id' => config('subscriptions.memberships.default_id')]);
        }

        $transactions->update(['active' => 0]);
    }

    /**
     * Process ad-promotion transactions and promote associated posts.
     *
     * @return array
     */
    public static function processAdPromotionTransactions()
    {
        $results = [];

        $now = Carbon::now();

        $transactions = self::valid('ad-promotion')
            ->with('post')
            ->get()
            ->toArray();

        $results = array_map(function($transaction) use($now){
            if (Post::whereId($transaction['post_id'])->exists()) {
                $post = Post::find($transaction['post_id']);

                $postBumpedAt = $post->bumped_at;

                $nextPostBumpedAt = Carbon::parse($postBumpedAt)->addDay();

                if ($nextPostBumpedAt <= $now) {
                    $post->bumped_at = $now;
                    $post->save();

                    // Promotion is successful.
                    return [
                        'successful' => true,
                        'output' => 'Ad/Post (id:' . $transaction['post_id'] . ') of the User (id:' . $transaction['user_id'] . ') is promoted as Bump-Ad on ' . $now . '.'
                    ];
                }
            } else {
                // Return error.
                return [
                    'successful' => false,
                    'output' => '`Bump Ad` Promotion Error: Post (id:' . $transaction['post_id'] . ') does not exist.'
                ];
            }

            return null;
        }, $transactions);

        $filteredResults = array_filter($results, function ($result) {
            // Keep only non-null values
            return $result !== null;
        });

        return $filteredResults;
    }

}
