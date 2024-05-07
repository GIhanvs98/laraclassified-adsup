<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AdminSettingsGeneralPostAdFeatures extends Component
{
    #[Validate('boolean')]
    public bool $compress_images;

    public $settings_overwritten = [];

    #[Validate('boolean')]
    public bool $add_watermark;

    #[Validate('boolean')]
    public bool $otp_verification;

    #[Validate('boolean')]
    public bool $guest_ads;

    public bool $successfull = false;

    public bool $error = false;

    public $error_message;

    public array $modal;

    public const USER_SETTINGS_OPTIONS = [
        'TRUE' => 'true',
        'FALSE' => 'false',
        'SYSTEM_DEFAULT' => 'system_default',
    ];

    public function render()
    {
        $this->settings_overwritten = [
            'compress_images' => User::where('compress_images', '<>', self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'])->whereNull('deleted_at')->get(),
            'watermark_images' => User::where('watermark_images', '<>', self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'])->whereNull('deleted_at')->get(),
            'otp_verify' => User::where('otp_verify', '<>', self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'])->whereNull('deleted_at')->get(),
        ];

        return view('livewire.admin-settings-general-post-ad-features', [
            'settings_overwritten' => $this->settings_overwritten,
        ]);
    }

    public function mount()
    {
        $this->modal = [
            'state' => null, # edit/new/null
            'setting' => "", # compress_images/watermark_images/otp_verify/null
            'users' => null,
            'values' => [
                'user' => "",
                'setting' => "",
            ],
        ];

        $this->settings_overwritten = [
            'compress_images' => [],
            'watermark_images' => [],
            'otp_verify' => [],
        ];

        if (option_exists('allow-image-compression')) {

            $this->compress_images = option('allow-image-compression');

        } else {

            $this->compress_images = true;

            option(['allow-image-compression' => $this->compress_images]);
        }

        if (option_exists('allow-image-watermark')) {

            $this->add_watermark = option('allow-image-watermark');

        } else {

            $this->add_watermark = true;

            option(['allow-image-watermark' => $this->add_watermark]);
        }

        if (option_exists('allow-otp-verification')) {

            $this->otp_verification = option('allow-otp-verification');

        } else {

            $this->otp_verification = false;

            option(['allow-otp-verification' => $this->otp_verification]);
        }

        if (option_exists('allow-guest-ads')) {

            $this->guest_ads = option('allow-guest-ads');

        } else {

            $this->guest_ads = false;

            option(['allow-guest-ads' => $this->guest_ads]);
        }
    }

    public function updatedCompressImages()
    {
        $this->successfull = false;

        $this->error = false;

        try {

            option(['allow-image-compression' => $this->compress_images]);

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }
    }

    public function updatedAddWatermark()
    {
        $this->successfull = false;

        $this->error = false;

        try {

            option(['allow-image-watermark' => $this->add_watermark]);

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }
    }

    public function updatedOtpVerification()
    {
        $this->successfull = false;

        $this->error = false;

        try {

            option(['allow-otp-verification' => $this->otp_verification]);

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }
    }


    public function updatedGuestAds()
    {
        $this->successfull = false;

        $this->error = false;

        try {

            option(['allow-guest-ads' => $this->guest_ads]);

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }
    }

    public function resetDefault()
    {
        $this->successfull = false;

        $this->error = false;

        try {

            $this->validate();

            $this->compress_images = true;

            $this->add_watermark = true;

            $this->otp_verification = false;

            $this->guest_ads = false;

            option(['allow-image-compression' => $this->compress_images]);

            option(['allow-image-watermark' => $this->add_watermark]);

            option(['allow-otp-verification' => $this->otp_verification]);

            option(['allow-guest-ads' => $this->guest_ads]);

            $this->successfull = true;

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }
    }

    #[On('show-modal')]
    public function showModal(string $type, string $setting = null, string $userId = null)
    {
        $this->modal['state'] = $type;

        $this->modal['setting'] = $setting;

        if ($type == "new") {

            $this->modal['users'] = User::where($setting, self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'])->whereNull('deleted_at')->get();

        } else if ($type == "edit") {

            $this->modal['users'] = User::where($setting, '<>', self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'])->whereNull('deleted_at')->get();

            $this->modal['values']['user'] = $userId;

        } else {

            $this->modal['state'] = null;

            $this->dispatch('hide-modal');
        }
    }

    public function submit()
    {
        $this->validate([
            'modal.setting' => ['required', Rule::in(['compress_images', 'add_watermark', 'otp_verification'])],
            'modal.values.user' => ['required', 'exists:users,id'],
            'modal.values.setting' => ['boolean'],
        ], [], [
            'modal.setting' => 'setting',
            'modal.values.user' => 'user',
            'modal.values.setting' => 'setting value',
        ]);

        try {

            $user = User::find($this->modal['values']['user']);

            if ($this->modal['setting']) {

                $user->{$this->modal['setting']} = $this->modal['values']['setting'] ? self::USER_SETTINGS_OPTIONS['TRUE'] : self::USER_SETTINGS_OPTIONS['FALSE'];
            }

            $user->save();

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }

        $this->modal['state'] = null;

        $this->dispatch('hide-modal');

    }

    public function setDefault(string $setting, string $userId = null)
    {
        try {

            $user = User::find($userId);

            if ($setting) {

                $user->{$setting} = self::USER_SETTINGS_OPTIONS['SYSTEM_DEFAULT'];
            }

            $user->save();

        } catch (\Throwable $th) {

            $this->error = true;

            $this->error_message = config('app.debug') ? $th->getMessage() : 'Unexpected error occured. Please try again!';
        }

    }
}
