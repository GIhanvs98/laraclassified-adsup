<?php

namespace App\Livewire;

use Livewire\Attributes\Rule;
use Livewire\Component;
use App\Classes\SMS;

class SendOtp extends Component
{
    public $title;

    public $event;

    public $parentStyle;

    public $labelClass;

    public $inputClass;

    public $readonlyClass;

    public $buttonClass;

    #[Rule('required|numeric|min:10')]
    public $phone;

    #[Rule('required|numeric|digits:6')]
    public $otp;

    public $otpGenerated;

    protected $sms;

    public bool $step_1 = true;

    public bool $step_2 = false;

    public bool $success = false;

    public function render()
    {
        return view('livewire.send-otp');
    }

    public function mount(
        $title,
        $verified,
        $value,
        $event,
        $parentStyle,
        $inputClass,
        $readonlyClass,
        $labelClass,
        $buttonClass
    ) {
        $this->title = $title;

        $this->phone = $value;

        $this->event = $event;

        $this->parentStyle = $parentStyle;

        $this->inputClass = $inputClass;

        $this->readonlyClass = $readonlyClass;

        $this->labelClass = $labelClass;

        $this->buttonClass = $buttonClass;

        if ($verified) {

            $this->step_1 = false;

            $this->step_2 = false;

            $this->success = true;
        }

    }

    protected $listeners = ['edit-contact-number' => 'editContactNumber'];

    public function editContactNumber()
    {
        $this->phone = null;

        $this->step_1 = true;

        $this->step_2 = false;

        $this->success = false;
    }

    public function send()
    {

        $validated = $this->validate(['phone' => 'required|numeric|min:10']);

        try {

            $this->otp = null;

            $this->sms = new SMS;

            $response = $this->sms->otp()->send($this->phone);

            $this->otpGenerated = $this->sms->getOTP();

            if ($response->ok()) {

                $this->step_1 = false;

                return $this->step_2 = true;
            }

        } catch (\Throwable $th) {

            if (config('app.debug')) {
                return $this->addError('phone', $th->getMessage());
            } else {
                return $this->addError('phone', 'Unexpected error occured. Please try again!');
            }
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

            if ($this->otp == $this->otpGenerated) {

                $this->dispatch($this->event, $this->phone);

                $this->step_1 = false;

                $this->step_2 = false;

                return $this->success = true;

            } else {

                $this->addError('otp', 'Invalid OTP number.');
            }

        } catch (\Throwable $th) {

            if (config('app.debug')) {
                return $this->addError('phone', $th->getMessage());
            } else {
                return $this->addError('phone', 'Unexpected error occured. Please try again!');
            }
        }

    }
}