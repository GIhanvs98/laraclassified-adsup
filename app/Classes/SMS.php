<?php

namespace App\Classes;

use App\Traits\SMS\Ozonedesk;
use App\Traits\SMS\SMSHub;

/**
 * Select App\Traits\SMS\SMSHub or App\Traits\SMS\SMSHub or define your custom sms gateway.
 */

class SMS
{
    use Ozonedesk;

    protected $otp;

    protected $message;

    public function otp(int $min = 100000, int $max = 999999)
    {

        $this->otp = mt_rand($min, $max);

        $this->message = 'Your OTP is: ' . $this->otp;

        return $this;
    }

    public function sms(string $message)
    {
        $this->message = $message;

        return $this;
    }

    public function getOTP()
    {
        return $this->otp;
    }
}