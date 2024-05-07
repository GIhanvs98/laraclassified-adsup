<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Rules\PhoneLenght;
use App\Rules\PhoneCountry;
use App\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use App\Classes\SMS;

class Register extends Component
{

    #[Rule('required|min:3|max:100')]
    public $name;

    #[Rule(['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:' . User::class])]
    public $email;

    #[Rule(['required', 'min:3', 'max:60', new PhoneCountry, new PhoneLenght])]
    public $phone;

    #[Rule('required|same:phone')]
    public $contact_number;

    #[Rule(['required', 'string', new Password])]
    public $password;

    #[Rule('required|same:password')]
    public $password_confirmation;

    #[Rule('accepted')]
    public $accept_terms;

    #[Rule('accepted')]
    public $accept_marketing_offers;

    #[Rule('required|numeric|digits:6')]
    public $otp;

    protected $sms;

    public bool $step_1 = true;

    public bool $step_2 = false;

    public bool $success = false;

    public function render()
    {
        return view('livewire.register');
    }

    public function editContactNumber()
    {
        $this->phone = null;

        $this->step_1 = true;

        $this->step_2 = false;

        $this->success = false;
    }

    public function send()
    {

        $validated = $this->validate(['phone' => ['required', 'min:3', 'max:60', new PhoneCountry, new PhoneLenght]]);

        try {

            $this->otp = null;

            $this->sms = new SMS;

            $response = $this->sms->otp()->send(substr_replace($validated['phone'], "0", 0, 3));

            session(['otp-generated' => Hash::make($this->sms->getOTP())]);

            if ($response->ok()) {

                $this->step_1 = false;

                return $this->step_2 = true;
            }

        } catch (\Throwable $th) {

            // return $this->addError('phone', $th->getMessage());

            return $this->addError('phone', 'Unexpected error occured. Please try again!');
        }
    }

    public function resendOTP()
    {
        $this->send();
    }

    public function verify()
    {

        $validated = $this->validate(['otp' => 'required|numeric|digits:6']);

        try {

            if (Hash::check($this->otp, session('otp-generated'))) {

                $this->step_1 = false;

                $this->step_2 = false;

                $this->contact_number = $this->phone;

                return $this->success = true;

            } else {

                $this->addError('otp', 'Invalid OTP number.');
            }

        } catch (\Throwable $th) {

            return $this->addError('phone', $th->getMessage());
        }

    }

    public function register()
    {

        $validated = $this->validate();

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['phone_verified_at'] = now();
        $validated['country_code'] = strtoupper(config('sms.country-code-symbol'));
        $validated['phone_country'] = strtolower(config('sms.country-code-symbol'));
        $validated['language_code'] = "en";
        $validated['phone_national'] = phoneNational($validated['phone'], getPhoneCountry());

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}
