<?php

/*
/   This Controller is only allocated for payment gateway `callback` handling. So use with care.
/
*/

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Package;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function callback(Request $request){

        /*
            {
                "transaction_id": "E2D51187B9A137DB7E867", 
                "pl_ref_no": "", 
                "status": 1, 
                "status_message": "SUCCESS",
                "additional_data": "transaction_id:1|token:7FdOhDGseBjxovTUyv56oXKOw8QIAA"
            }   
        */

        /*

           // This is for test purposes.
           // --------------------   
           // Log file - "./storage/logs/delete-expired-ads.log"   
        
           // Get all request data
           $requestData = $request->all();

           // Log the request data to a file
           Log::info('Payment Callback from onepay.lk:', $requestData);
        
        */
        
        if($request->status == 1){
    
            $appToken = config('onepay-lk-payment.app_payment_callback_bearer_token');

            $additional_data = stringToArray($request->additional_data);

            $receivedToken = $additional_data['token'];

            if($appToken == $receivedToken){

                $transaction_id = $additional_data['transaction_id'];

                // Transaction process ------------------------------------------------------------------

                try {

                    $transaction = Transaction::find($transaction_id);

                    // ------------------------------------------------------------------
                    //            Check if exists :- Memberships or Packages
                    // ------------------------------------------------------------------
                    
                    if($transaction->payment_type == "membership"){

                        // Only for memberships.
                        $user = User::where('id', $transaction->user->id);

                        if($user->doesntExist()){
                            // User do not exists
                            // Decline the purchase

                            $transaction->payment_status = "declined";

                            $transaction->active = 0;

                            $transaction->save();
                            
                            return response()->json([
                                "success" => false,
                                'error' => 'payment-declined',
                                'message' => 'Transaction is declined due to the absance of the given user.',
                            ], 500);
                        }

                        $user = $user->first();
                        
                        // **********************************************************************
                        // ------------------------ Start for Memberships -----------------------
                        // **********************************************************************

                        // Membership validation

                        $membership = Membership::where('id', $transaction->membership_id);

                        if($membership->doesntExist()){
                            // Membership do not exists
                            // Decline the purchase

                            $transaction->payment_status = "declined";

                            $transaction->active = 0;

                            $transaction->save();
                            
                            return response()->json([
                                "success" => false,
                                'error' => 'payment-declined',
                                'message' => 'Transaction is declined due to the absance of the given membership.',
                            ], 500);

                        }


                        // Transaction validation

                        $last_transaction = Transaction::successfull('membership')->where('id', $transaction_id)->orderBy('created_at', 'desc')->first();

                        if ($last_transaction) {
                            
                            $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

                            if ( $paymentValidUntillDatetime->greaterThan(now()) ) {
                                // Allready purchase error for membership

                                $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');
                                        
                                return response()->json([
                                    "success" => false,
                                    'message' => 'Your current membership is valid untill '.$payment_valid_untill_datetime.'. You can change or renew your membership from '.$payment_valid_untill_datetime.' onwards.',
                                ], 500);
                            }
                        }

                        // **********************************************************************
                        // -------------------------- End for Memberships -----------------------
                        // **********************************************************************

                    }else if($transaction->payment_type == "ad-promotion"){

                        // **********************************************************************
                        // ---------------------- Start for Ad Promotions -----------------------
                        // **********************************************************************


                        // Post Validation for existance

                        $post = DB::table('posts')->where('id', $transaction->post->id);

                        if ($post->doesntExist()) {

                            Log::error('`unkown-post` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);
      
                            return response()->json([
                                "success" => false,
                                'error' => 'unkown-post',
                                'message' => 'Given post is currently unavailable.',
                            ], 500);
                        }

                        $post = Post::where('id', $transaction->post->id);

                        if ($post->doesntExist()) {

                            Log::error('`unverified-user` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);
      
                            return response()->json([
                                "success" => false,
                                'error' => 'unverified-user',
                                'message' => 'You must verify your account email and contact numbers.',
                            ], 500);
                        }


                        // Package validation for existance

                        $package = Package::where('id', $transaction->package->id);

                        if ($package->doesntExist()) {

                            Log::error('`unkown-package` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);
      
                            return response()->json([
                                "success" => false,
                                'error' => 'unkown-package',
                                'message' => 'Given Ad promotion is currently unavailable.',
                            ], 500);
                        }


                        // Transaction validation

                        $last_transaction = Transaction::successfull('ad-promotion')->where('id', $transaction_id)->orderBy('created_at', 'desc')->first();

                        if ($last_transaction) {

                            $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

                            if ( $paymentValidUntillDatetime->greaterThan(now()) ) {
                                // Allready purchase error for ad-promotion

                                $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');
                                  
                                Log::error('`promotions-allready-added-error` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);
      
                                return response()->json([
                                    "success" => false,
                                    'message' => 'Your current Ad promotion for the given post is valid untill '.$payment_valid_untill_datetime.'. You can change or renew your ad promotion for your post from '.$payment_valid_untill_datetime.' onwards.',
                                ], 500);
                            }
                        }

                        // **********************************************************************
                        // ------------------------ End for Ad Promotions -----------------------
                        // **********************************************************************

                    }else{

                        Log::warning('`unkwon-transaction-type` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                        return response()->json([
                            "success" => false,
                            'error' => 'unkwon-transaction-type',
                            'message' => 'Transaction method is unknown.',
                        ], 500);
                    }

                    // Use real date time for payment renewal
                    
                    // Using Carbon -------------------------------------------------------------

                    $payment_started_datetime = Carbon::now();

                    if($transaction->payment_type == "membership"){

                        $nextPaymentMonth = $payment_started_datetime->settings(['monthOverflow' => false])->addMonths(config('subscriptions.memberships.adding_months'));

                        $nextPaymentDay = Carbon::parse($nextPaymentMonth)->settings(['monthOverflow' => false])->addDays(config('subscriptions.memberships.payment_pending_time'));

                    }

                    if($transaction->payment_type == "ad-promotion"){

                        $nextPaymentMonth = $payment_started_datetime->settings(['monthOverflow' => false])->addDays($package->first()->promo_duration); // Refer `packages` table.

                        $nextPaymentDay = Carbon::parse($nextPaymentMonth)->settings(['monthOverflow' => false])->addDays(config('subscriptions.ad-promotions.payment_pending_time'));

                    }

                    // ---------------------------------------------------------------------------


                    // Custom inbuild function from kenura ---------------------------------------
                    
                    /*
                        
                        $payment_started_datetime = now();

                        if($transaction->payment_type == "membership"){

                            $nextPaymentMonth = nextPaymentDate( $payment_started_datetime, 0, config('subscriptions.memberships.adding_months'), config('subscriptions.memberships.precision'));

                            $nextPaymentDay = nextPaymentDay( $nextPaymentMonth, config('subscriptions.memberships.payment_pending_time'), config('subscriptions.memberships.precision'));
                            
                        }

                        if($transaction->payment_type == "ad-promotion"){

                            $nextPaymentMonth = nextPaymentDay( $payment_started_datetime, 0, $package->first()->promo_duration, config('subscriptions.memberships.precision'));

                            $nextPaymentDay = nextPaymentDay( $nextPaymentMonth, config('subscriptions.memberships.payment_pending_time'), config('subscriptions.memberships.precision'));
                            
                        }

                    */

                    // ---------------------------------------------------------------------------

                    $transaction->payment_status = "success";

                    $transaction->response_transaction_id = $request->transaction_id;

                    $transaction->active = 1;

                    $transaction->payment_valid_untill_datetime = $nextPaymentMonth;

                    $transaction->payment_due_datetime = $nextPaymentDay;

                    $transaction->save();

                    // ------------------------------------------------------------------------
                    //    Save data in user for Memberships or save data in post for Packages
                    // ------------------------------------------------------------------------
                    
                    if($transaction->payment_type == "membership"){

                        // **********************************************************************
                        // ------------------------ Start for Memberships -----------------------
                        // **********************************************************************

                        $membership = $membership->first();

                        $user->membership()->associate($membership);

                        $user->save();

                        $dt = Carbon::create($transaction->payment_valid_untill_datetime);

                        $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');
                        
                        Log::info('`transaction-successfull` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                        return response()->json([
                            "success" => true,
                            'message' => 'You have successful purchased the `'.$user->membership->name.'` membership. Your purcase will be valid untill '.$payment_valid_untill_datetime.'.',
                        ], 200);

                        // **********************************************************************
                        // -------------------------- End for Memberships -----------------------
                        // **********************************************************************

                    }else if($transaction->payment_type == "ad-promotion"){

                        // **********************************************************************
                        // ---------------------- Start for Ad Promotions -----------------------
                        // **********************************************************************

                        $post = $post->first();

                        $package = $package->first();


                        // If package_type = "Bump Ad" => update :- $post->bumped_at = now(); also.

                        // This value will be updated `daily` untill transaction_end_date.
                        
                        if($package->packge_type == "Bump Ads"){

                            $post->bumped_at = now();
                        }

                        $post->package()->associate($package);

                        $post->save();

                        $dt = Carbon::create($transaction->payment_valid_untill_datetime);

                        $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');
                        
                        Log::info('`transaction-successfull` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                        return response()->json([
                            "success" => true,
                            'message' => 'You have successful purchased the `'.$post->package->name.'` Ad promotion. Your purcase will be valid untill '.$payment_valid_untill_datetime.'.',
                        ], 200);

                        // **********************************************************************
                        // ------------------------ End for Ad Promotions -----------------------
                        // **********************************************************************

                    }else{

                        Log::error('`unkwon-transaction-type` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                        return response()->json([
                            "success" => false,
                            'error' => 'unkwon-transaction-type',
                            'message' => 'Transaction method is unknown.',
                        ], 500);
                    }

                } catch (\Throwable $th) {

                    Log::error('`unexpected-error`:{error} , for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['error' => $th->getMessage(), 'transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                    return response()->json([
                        "success" => false,
                        'error' => 'unexpected-error',
                        'message' => 'But dont worry, you can go back to the home page.',
                    ], 500);
                }

            }else{

                Log::warning('`Unautherized access` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

                return response()->json([
                    "success" => false,
                    'error' => 'unauthorized',
                    'message' => 'Unautherized access.',
                ], 401);
            }

        }else{

            Log::error('`request->status: 1` for the transaction callback for transaction:{transaction_id}. The response from onepay.lk is:{response} : ', ['transaction_id' => stringToArray($request->additional_data)['transaction_id'], 'response' => $request->all() ]);

            return response()->json([
                "success" => false,
                'error' => 'unexpected-error',
                'message' => 'But dont worry, you can go back to the home page.',
            ], 500);
        }
    }
}
