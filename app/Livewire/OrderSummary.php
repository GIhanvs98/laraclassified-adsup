<?php

namespace App\Livewire;

use App\Classes\OnepayLKPayment;
use App\Models\Membership;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Post;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Illuminate\Support\Carbon;
use App\Rules\PhoneLenght;
use App\Rules\PhoneCountry;
use Closure;

class OrderSummary extends Component
{

    public $data;

    public $post; // If only its `ad-promotions`

    public string $order_type;
    public int $order_id;

    public $error_output = null;

    public $referenceId;

    public bool $emailDisabled = true;

    #[Rule('required|email:rfc,dns')]
    public $email;

    public bool $nameDisabled = true;

    #[Rule('required|min:3|max:100')]
    public $name;

    public bool $contactNumberDisabled = true;

    #[Rule(['required', 'min:3', 'max:60', new PhoneCountry, new PhoneLenght])]
    public $contact_number;
    public $phone;
    public $phone_alternate_1;
    public $phone_alternate_2;

    public function render()
    {
        return view('livewire.order-summary');
    }

    public function mount($ordertype, $orderid, $data)
    {

        $this->order_type = $ordertype;
        $this->order_id = $orderid;
        $this->data = $data;
        $this->referenceId = generateReferenceNumber(5);

        /* 
        / User verification is not required here if the user is logged in.
        / If the user is not logged in 
        /   
        /   membership :- Redirect to membership page.
        */

        if ($ordertype == "memberships") {

            // Data can be loaded since allready checked if authenticated in the controller.
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;

            if (isset(auth()->user()->phone)) {

                $this->contact_number = auth()->user()->phone;
            } else if (isset(auth()->user()->phone_alternate_1)) {

                $this->contact_number = auth()->user()->phone_alternate_1;
            } else if (isset(auth()->user()->phone_alternate_2)) {

                $this->contact_number = auth()->user()->phone_alternate_2;
            } else {

                $this->contact_number = null;
            }

            $this->phone = auth()->user()->phone;

            $this->phone_alternate_1 = auth()->user()->phone_alternate_1;

            $this->phone_alternate_2 = auth()->user()->phone_alternate_2;
            
        } else if ($ordertype == "ad-promotions") {

            // Post detais `$data` and package data are allready validated from the controller.

            if (auth()->check()) {

                // Load user data `if logged in`.
                $this->name = auth()->user()->name;
                $this->email = auth()->user()->email;

                if (isset(auth()->user()->phone)) {

                    $this->contact_number = auth()->user()->phone;
                } else {

                    $this->contact_number = null;
                }

                $this->phone = auth()->user()->phone;

                $this->phone_alternate_1 = auth()->user()->phone_alternate_1;

                $this->phone_alternate_2 = auth()->user()->phone_alternate_2;

            } else if (request()->session()->has('guest-user')) {

                // Guest user exists

                $guestUser = session('guest-user');

                // Load user data `if stored in session if its a guest`
                $this->name = $guestUser->name;
                $this->email = $guestUser->email;
                $this->phone = $guestUser->phone;

                if (isset($guestUser->phone)) {

                    $this->contact_number = $guestUser->phone;
                } else {

                    $this->contact_number = null;
                }

            } else {
                // No user information. User has to enter all the data.
            }

            // Switch values. isince ad-promtion involve with `posts` and `packages tables` while other only with the `memberships` table.

            $this->post = $this->data;
            $this->data = Package::find($orderid);
        }

        // check contact number
        if (!isset($this->contact_number)) {
            $this->contactNumberDisabled = false;
        }

        // check email
        if (!isset($this->email)) {
            $this->emailDisabled = false;
        }

        // check name
        if (!isset($this->name)) {
            $this->nameDisabled = false;
        }
    }

    public function toggleContactNumberDisabled()
    {
        $this->contactNumberDisabled = !$this->contactNumberDisabled;
        $this->contact_number = null;
    }

    public function proceedPackagePayment()
    {
        $last_transaction = Transaction::successfull('ad-promotion')->where('post_id', $this->post->id)->orderBy('created_at', 'desc')->first();

        if ($last_transaction) {

            $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

            if ($paymentValidUntillDatetime->greaterThan(now())) {
                // Allready purchase error for membership

                $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                return $this->error_output = 'Your current package for the Ad is valid untill ' . $payment_valid_untill_datetime . '. You can change or renew your package from ' . $payment_valid_untill_datetime . ' onwards.';
            }
        }

        $validated = $this->validate();

        if ($this->order_type == "ad-promotions") {

            $package = Package::find($this->order_id);

            if ($package->currency_code != "LKR") {

                return $this->error_output = "LKR payments are only accepted.";
            }
        } else {

            return $this->error_output = "Unexpected payment error occured! Try again.";
        }

        $firstname = split_name($this->name)[0];
        $lastname = split_name($this->name)[1];

        if (!isset(split_name($this->name)[1])) {
            $lastname = fake()->lastName();
        }

        $transaction = new Transaction;

        $package = Package::find($this->order_id);

        if (auth()->check()) {

            $user = User::find(auth()->user()->id);

            $transaction->user()->associate($user);

        }

        $transaction->name = $this->name;

        $transaction->email = $this->email;

        $transaction->phone = $this->contact_number;

        $transaction->payment_type = "ad-promotion";

        $transaction->post()->associate($this->post);

        $transaction->package()->associate($package);

        $transaction->payment_status = "pending";

        $transaction->currency()->associate($package->currency);

        $transaction->reference_id = $this->referenceId;

        $transaction->payment_started_datetime = Carbon::now();

        $transaction->save();

        $appToken = config('onepay-lk-payment.app_payment_callback_bearer_token');

        $additional_data = [
            "transaction_id" => $transaction->id,
            "token" => $appToken,
        ];

        $payment = new OnepayLKPayment;

        $payment->amount = $package->price;
        $payment->reference = $this->referenceId;
        $payment->customer_first_name = $firstname;
        $payment->customer_last_name = $lastname;
        $payment->customer_phone_number = $this->contact_number;
        $payment->customer_email = $this->email;
        $payment->transaction_redirect_url = route('ad-promotions.payment.response', ['transaction_id' => $transaction->id]);
        $payment->additional_data = ArrayToString($additional_data);

        $payment->initPayment();

        try {

            $payment->sendPaymentRequest();

            if ($payment->result['status'] == 1000) {

                $data = $payment->result['data'];

                try {

                    $transaction->ipg_transaction_id = $data['ipg_transaction_id'];

                    $transaction->gross_amount = $data['amount']['gross_amount'];

                    $transaction->discount = $data['amount']['discount'];

                    $transaction->handling_fee = $data['amount']['handling_fee'];

                    $transaction->net_amount = $data['amount']['net_amount'];

                    $transaction->save();
                } catch (\Throwable $th) {

                    return $this->error_output = "Unexpected error occured! Try again.";
                }
            } else {

                return $this->error_output = "Unexpected payment error occured! Please contact us for help and support.";
            }
        } catch (\Throwable $th) {

            return $this->error_output = "Unexpected error occured! Try again.";
        }

        // Adding the transaction id. it is a different session storage.
        session(['ad-promotions.transactionId' => $transaction->id]);


        return $payment->processPayment();
    }

    public function proceedMembershipPayment()
    {
        $last_transaction = Transaction::successfull('membership')->where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();

        if ($last_transaction) {

            $paymentValidUntillDatetime = Carbon::parse($last_transaction->payment_valid_untill_datetime);

            if ($paymentValidUntillDatetime->greaterThan(now())) {
                // Allready purchase error for membership

                $dt = Carbon::create($last_transaction->payment_valid_untill_datetime);

                $payment_valid_untill_datetime = $dt->format('l jS \\of F Y h:i:s A');

                return $this->error_output = 'Your current membership is valid untill ' . $payment_valid_untill_datetime . '. You can change or renew your membership from ' . $payment_valid_untill_datetime . ' onwards.';
            }
        }

        $validated = $this->validate();

        if ($this->order_type == "memberships") {

            $membership = Membership::find($this->order_id);

            if ($membership->currency_code != "LKR") {

                return $this->error_output = "LKR payments are only accepted.";
            }
        } else {

            return $this->error_output = "Unexpected payment error occured! Try again.";
        }

        $firstname = split_name($this->name)[0];
        $lastname = split_name($this->name)[1];

        if (!isset(split_name($this->name)[1])) {
            $lastname = fake()->lastName();
        }

        $transaction = new Transaction;

        $user = User::find(auth()->user()->id);

        $membership = Membership::find($this->order_id);

        $transaction->user()->associate($user);

        $transaction->name = $this->name;

        $transaction->email = $this->email;

        $transaction->phone = $this->contact_number;

        $transaction->payment_type = "membership";

        $transaction->membership()->associate($membership);

        $transaction->payment_status = "pending";

        $transaction->currency()->associate($membership->currency);

        $transaction->reference_id = $this->referenceId;

        $transaction->payment_started_datetime = Carbon::now();

        $transaction->save();

        $appToken = config('onepay-lk-payment.app_payment_callback_bearer_token');

        $additional_data = [
            "transaction_id" => $transaction->id,
            "token" => $appToken,
        ];

        $payment = new OnepayLKPayment;

        $payment->amount = $membership->amount;
        $payment->reference = $this->referenceId;
        $payment->customer_first_name = $firstname;
        $payment->customer_last_name = $lastname;
        $payment->customer_phone_number = $this->contact_number;
        $payment->customer_email = $this->email;
        $payment->transaction_redirect_url = route('memberships.payment.response', ['transaction_id' => $transaction->id]);
        $payment->additional_data = ArrayToString($additional_data);

        $payment->initPayment();

        try {

            $payment->sendPaymentRequest();

            if ($payment->result['status'] == 1000) {

                $data = $payment->result['data'];

                try {

                    $transaction->ipg_transaction_id = $data['ipg_transaction_id'];

                    $transaction->gross_amount = $data['amount']['gross_amount'];

                    $transaction->discount = $data['amount']['discount'];

                    $transaction->handling_fee = $data['amount']['handling_fee'];

                    $transaction->net_amount = $data['amount']['net_amount'];

                    $transaction->save();
                } catch (\Throwable $th) {

                    return $this->error_output = "Unexpected error occured! Try again.";
                }
            } else {

                return $this->error_output = "Unexpected payment error occured! Please contact us for help and support.";
            }
        } catch (\Throwable $th) {

            return $this->error_output = "Unexpected error occured! Try again.";
        }

        return $payment->processPayment();
    }
}
