<?php

namespace App\Http\Controllers\Web\Public;

use Larapen\LaravelMetaTags\Facades\MetaTag;
use App\Classes\OnepayLKPayment;
use App\Models\GuestAdPromotionLink;
use App\Models\Membership;
use App\Models\Package;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderSummaryController extends FrontController
{

    public function checkout(Request $request, string $order_type, int $order_id, ?int $post_id = null)
    {

        $data = null;

        if ($order_type == "memberships") {

            if (!auth()->check()) {
                return redirect()->route('login');
            }

            $data = Membership::where('id', $order_id);

            if ($data->doesntExist()) {
                return abort(404);
            }

            $data = $data->first();

            if ($data->name == "Non Member" || $data->amount == "0.00") {
                return abort(404);
            }

            // Check if allready purchased a membership
            $last_transaction = Transaction::successfull('membership')->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();

            if ($last_transaction) {

                $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

                if ($paymentValidUntillDatetime->greaterThan(now())) {
                    // Allready purchase error for membership

                    $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                    $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                    return view('pages.order-summary-error', [
                        'section' => 'allready-purchased',
                        'message' => 'Your current membership is valid untill ' . $payment_valid_untill_datetime . '. You can change or renew your membership from ' . $payment_valid_untill_datetime . ' onwards.',
                    ]);
                }
            }
        }

        if ($order_type == "ad-promotions") {

            // --------------------------------- Start validating the post ---------------------------------

            $postId = $post_id;

            $data = DB::table('posts')->where('id', $postId);

            if ($data->doesntExist()) {

                return abort(404);
            }

            $data = Post::where('id', $postId);

            if ($data->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'user-unverified',
                ]);
            }

            // --------------------------------- End validating the post ---------------------------------


            // --------------------------------- Start validating the package ---------------------------------

            if (Package::whereId($order_id)->doesntExist()) {

                return redirect()->route('post-ad.promote', ['postId' => $postId]);
            }

            // --------------------------------- End validating the package ---------------------------------

            $data = $data->first(); // Geting the post data

            // ----------------------- Start validating whether post already promoted -----------------------

            $last_transaction = Transaction::successfull('ad-promotion')->where('post_id', $postId)->orderBy('created_at', 'desc')->first();

            if ($last_transaction) {

                $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

                if ($paymentValidUntillDatetime->greaterThan(now())) {

                    // Post is allready promoted.

                    $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                    $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                    return view('pages.order-summary-error', [
                        'section' => 'allready-purchased',
                        'category' => 'ad-promotions',
                        'message' => 'Your current package for the Ad is valid untill ' . $payment_valid_untill_datetime . '. You can change or renew your package from ' . $payment_valid_untill_datetime . ' onwards.',
                    ]);
                }
            }

            // ----------------------- End validating whether post already promoted -----------------------


            // ----------------------- Start checking if the post is reviewed -----------------------

            if (!isset($data->reviewed_at)) {

                return view('pages.order-summary-error', [
                    'section' => 'ad-review-pending',
                    'message' => 'Your Ad is currently being reviewed. Please wait until your ad is reviewed for ad promotion.',
                ]);
            }

            // ----------------------- End checking if the post is reviewed -----------------------


            // ----------------------- Start if everything fails -----------------------

            if (empty($data)) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'An unexpected error occured! Please contact us.',
                ]);
            }

            // ----------------------- End if everything fails -----------------------

        }

        return view('pages.order-summary', [
            'order_type' => $order_type,
            'order_id' => $order_id,
            'data' => $data,
        ]);
    }

    public function checkoutError()
    {
        return view('pages.order-summary-error', [
            'section' => 'unexpected-error',
            'message' => 'this is the message',
        ]);
    }

    public function MembershipsResponse(string $transaction_id)
    {
        try {

            // Transaction is missing.
            if (Transaction::where('id', $transaction_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'Transaction is unavailable.',
                ]);
            }

            $transaction = Transaction::where('id', $transaction_id)->first();

            // User verification
            if (!(User::whereId($transaction->user_id)->exists() && (auth()->user()->id == $transaction->user_id))) {

                if (!(request()->session()->has('ad-promotions.transactionId') && (session('ad-promotions.transactionId') == $transaction->id))) {
                    // Unautherized access.
                    return view('pages.order-summary-error', [
                        'section' => 'user-unverified',
                        'message' => 'Unautherized access to the transaction.',
                    ]);
                }
            }

            // Membership verification
            if (Membership::where('id', $transaction->membership_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'message' => 'Membership is unavailable.',
                ]);
            }

            if ($transaction->payment_status == "success") {

                $dt = Carbon::create($transaction->payment_valid_untill_datetime);

                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                $membership = Membership::where('id', $transaction->membership_id)->first();

                return view('pages.order-summary-error', [
                    'section' => 'payment-successful',
                    'message' => 'You have successful purchased the `' . $membership->name . '` membership. Your purcase will be valid untill ' . $payment_valid_untill_datetime . '.',
                ]);

            } else if ($transaction->payment_status == "declined") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-declined',
                    'message' => 'Transaction is declined.',
                ]);

            } else if ($transaction->payment_status == "canceled") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-canceled',
                    'message' => 'Transaction is canceled.',
                ]);

            } else if ($transaction->payment_status == "pending") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-pending',
                    'message' => 'Transaction is still pending.',
                ]);

            } else {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'message' => 'Unexpected error occured.',
                ]);

            }
        } catch (\Throwable $th) {

            return view('pages.order-summary-error', [
                'section' => 'unexpected-error',
                'message' => 'But dont worry, you can go back to the memberships page.',
            ]);
        }
    }

    public function AdPromotionsResponse(string $transaction_id)
    {

        try {

            // Transaction is missing.
            if (Transaction::where('id', $transaction_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'Transaction is unavailable.',
                ]);
            }

            $transaction = Transaction::where('id', $transaction_id)->first();

            // User verification
            if (!(User::whereId($transaction->user_id)->exists() && (auth()->user()->id == $transaction->user_id))) {

                if (!(request()->session()->has('ad-promotions.transactionId') && (session('ad-promotions.transactionId') == $transaction->id))) {
                    // Unautherized access.
                    return view('pages.order-summary-error', [
                        'section' => 'user-unverified',
                        'message' => 'Unautherized access to the transaction.',
                    ]);
                }
            }

            // Post Validation for existance (Unverified)
            if (isset($transaction->post_id) && DB::table('posts')->where('id', $transaction->post_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'Given post is currently unavailable.',
                ]);
            }

            // Post Validation for existance (Verified)
            if (isset($transaction->post_id) && Post::where('id', $transaction->post_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'You must verify your account email and contact numbers.',
                ]);
            }

            // Package validation for existance
            if (Package::where('id', $transaction->package_id)->doesntExist()) {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'Given Ad promotion is currently unavailable.',
                ]);
            }

            if ($transaction->payment_status == "success") {

                $dt = Carbon::create($transaction->payment_valid_untill_datetime);

                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                $post = Post::where('id', $transaction->post->id)->first();

                $package = Package::where('id', $transaction->package->id)->first();

                return view('pages.order-summary-error', [
                    'section' => 'payment-successful',
                    'category' => 'ad-promotions',
                    'message' => 'You have successful purchased the `' . $package->name . '` package for your ad `' . $post->title . '`. Your purcase will be valid untill ' . $payment_valid_untill_datetime . '.',
                ]);

            } else if ($transaction->payment_status == "declined") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-declined',
                    'message' => 'Transaction is declined.',
                ]);

            } else if ($transaction->payment_status == "canceled") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-canceled',
                    'message' => 'Transaction is canceled.',
                ]);

            } else if ($transaction->payment_status == "pending") {

                return view('pages.order-summary-error', [
                    'section' => 'payment-pending',
                    'message' => 'Transaction is still pending.',
                ]);

            } else {

                return view('pages.order-summary-error', [
                    'section' => 'unexpected-error',
                    'category' => 'ad-promotions',
                    'message' => 'Unexpected error occured.',
                ]);
            }

        } catch (\Throwable $th) {

            return view('pages.order-summary-error', [
                'section' => 'unexpected-error',
                'category' => 'ad-promotions',
                'message' => 'But dont worry, you can go back to the home page. Please contact us for more information.',
            ]);
        }
    }
}
