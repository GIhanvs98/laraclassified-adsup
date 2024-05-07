<?php

namespace App\Traits;

use App\Classes\SMS;

trait ContactNumbersTrait
{
    /**
     * array of contact_numbers use for ad posting and editing.
     * 
     * @var array
     */
    public array $contact_numbers = [];

    /**
     * array of showable contact numbers where whey have the property => permanent: false and state => visibility: false
     * 
     * @var array
     */
    public array $showable_contact_numbers = [];

    /**
     * Return the initial state/structure of contact numbers.
     * @return array
     */
    public function getContactNumbers(): array
    {
        return (isset($this->contact_numbers) && !empty($this->contact_numbers)) ? $this->contact_numbers : [
            'contact_number_1' => [
                'id' => 'contact_number_1',
                'attribute_name' => 'Main Contact Number',
                'models' => [
                    'contact_number' => 'contact_numbers.contact_number_1.values.contact_number',
                    'otp' => 'contact_numbers.contact_number_1.values.otp',
                    'toggle_visibility' => 'contact_numbers.contact_number_1.states.visibility',
                ],
                'values' => [
                    'contact_number' => null,
                    'otp' => null,
                ],
                'rules' => [
                    'contact_number' => [
                        'required',
                        'numeric',
                        'min:10',
                        'different:contact_numbers.contact_number_2.values.contact_number,contact_numbers.contact_number_3.values.contact_number,contact_numbers.whatsapp_number.values.contact_number',
                    ],
                    'otp' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'digits:6',
                    ],
                ],
                'properties' => [
                    'otp_session_key' => 'contact_numbers.contact_number_1.otp',
                    'permanent' => true,
                    'countdown' => true, // Enable countdown feature
                    'toggle_visibility' => false, // Enable checkbox to show/hide
                ],
                'states' => [
                    'stage' => null,
                    'visibility' => true,
                    'countdown' => false,
                ],
            ],
            'whatsapp_number' => [
                'id' => 'whatsapp_number',
                'attribute_name' => 'Whatsapp Number',
                'models' => [
                    'contact_number' => 'contact_numbers.whatsapp_number.values.contact_number',
                    'otp' => 'contact_numbers.whatsapp_number.values.otp',
                    'toggle_visibility' => 'contact_numbers.whatsapp_number.states.visibility',
                ],
                'values' => [
                    'contact_number' => null,
                    'otp' => null,
                ],
                'rules' => [
                    'contact_number' => [
                        'nullable',
                        'numeric',
                        'min:10',
                        'different:contact_numbers.contact_number_2.values.contact_number,contact_numbers.contact_number_3.values.contact_number,contact_numbers.contact_number_1.values.contact_number',
                    ],
                    'otp' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'digits:6',
                    ],
                ],
                'properties' => [
                    'otp_session_key' => 'contact_numbers.whatsapp_number.otp',
                    'permanent' => true,
                    'countdown' => true, // Enable countdown feature
                    'toggle_visibility' => true, // Enable checkbox to show/hide
                ],
                'states' => [
                    'stage' => null,
                    'visibility' => false,
                    'countdown' => false,
                ],
            ],
            'contact_number_2' => [
                'id' => 'contact_number_2',
                'attribute_name' => 'Alternate Contact Number 1',
                'models' => [
                    'contact_number' => 'contact_numbers.contact_number_2.values.contact_number',
                    'otp' => 'contact_numbers.contact_number_2.values.otp',
                    'toggle_visibility' => 'contact_numbers.contact_number_2.states.visibility',
                ],
                'values' => [
                    'contact_number' => null,
                    'otp' => null,
                ],
                'rules' => [
                    'contact_number' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'min:10',
                        'different:contact_numbers.contact_number_1.values.contact_number,contact_numbers.contact_number_3.values.contact_number,contact_numbers.whatsapp_number.values.contact_number',
                    ],
                    'otp' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'digits:6',
                    ],
                ],
                'properties' => [
                    'otp_session_key' => 'contact_numbers.contact_number_2.otp',
                    'permanent' => false,
                    'countdown' => true, // Enable countdown feature
                    'toggle_visibility' => false, // Enable checkbox to show/hide
                ],
                'states' => [
                    'stage' => null,
                    'visibility' => false,
                    'countdown' => false,
                ],
            ],
            'contact_number_3' => [
                'id' => 'contact_number_3',
                'attribute_name' => 'Alternate Contact Number 2',
                'models' => [
                    'contact_number' => 'contact_numbers.contact_number_3.values.contact_number',
                    'otp' => 'contact_numbers.contact_number_3.values.otp',
                    'toggle_visibility' => 'contact_numbers.contact_number_3.states.visibility',
                ],
                'values' => [
                    'contact_number' => null,
                    'otp' => null,
                ],
                'rules' => [
                    'contact_number' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'min:10',
                        'different:contact_numbers.contact_number_2.values.contact_number,contact_numbers.contact_number_1.values.contact_number,contact_numbers.whatsapp_number.values.contact_number',
                    ],
                    'otp' => [
                        'sometimes',
                        'required',
                        'numeric',
                        'digits:6',
                    ],
                ],
                'properties' => [
                    'otp_session_key' => 'contact_numbers.contact_number_3.otp',
                    'permanent' => false,
                    'countdown' => true, // Enable countdown feature
                    'toggle_visibility' => false, // Enable checkbox to show/hide
                ],
                'states' => [
                    'stage' => null,
                    'visibility' => false,
                    'countdown' => false,
                ],
            ],
        ];
    }

    /**
     * Send a request for the otp number.
     * 
     * @param string $field_id
     * @return mixed
     */
    public function send(string $field_id, string $event = "start_timer_")
    {
        $field = $this->contact_numbers[$field_id];

        $model = $field['models']['contact_number'] ?? 'contact_numbers.phone.values.contact_number';

        $rules = $field['rules']['contact_number'] ?? 'required|numeric|min:10';

        $session_key = $field['properties']['otp_session_key'];

        $validated = $this->validate(
            rules: [$model => $rules],
            attributes: [$model => strtolower($field['attribute_name'])]
        );

        $contact_number = $validated['contact_numbers'][$field_id]['values']['contact_number'] ?? null;

        try {

            $sms = new SMS();

            $response = $sms->otp()->send($contact_number);

            $otpGenerated = $sms->getOTP();

            session([$session_key => $otpGenerated]);

            if ($response->ok()) {
                $this->contact_numbers[$field_id]['states']['stage'] = 'verifying';
                $this->contact_numbers[$field_id]['states']['countdown'] = true;
                $this->dispatch($event . $field_id);
            } else {
                $this->contact_numbers[$field_id]['states']['stage'] = null;
            }

        } catch (\Throwable $th) {

            return $this->addError($model, $th->getMessage());
            if (config('app.debug')) {
                return $this->addError($model, $th->getMessage());
            } else {
                return $this->addError($model, 'Unexpected error occured. Please try again!');
            }
        }
    }

    /**
     * resend a request for a new otp number
     * 
     * @param string $field_id
     * @return void
     */
    public function resendOTP(string $field_id): void
    {
        $this->send($field_id, "restart_timer_");
    }

    /**
     * verify the otp number in the server session with the otp entered to the values.
     * 
     * @param string $field_id
     * @param string|null $customCallback Call the custom callback function
     * @return mixed
     */
    public function verify(string $field_id, ?string $customCallback = null)
    {
        $field = $this->contact_numbers[$field_id];

        $model = $field['models']['otp'] ?? 'contact_numbers.phone.values.otp';

        $rules = $field['rules']['otp'] ?? 'required|numeric|digits:6';

        $otpGenerated = session($field['properties']['otp_session_key']);

        $validated = $this->validate(
            rules: [$model => $rules],
            attributes: [$model => 'otp']
        );

        $otp = $validated['contact_numbers'][$field_id]['values']['otp'] ?? null;

        try {

            if ($otp == $otpGenerated) {

                $this->contact_numbers[$field_id]['states']['stage'] = 'success';

                if ($customCallback !== null && is_callable([$this, $customCallback])) {

                    $this->$customCallback();
                }

            } else {
                $this->addError('otp', 'Invalid OTP number.');

                $this->contact_numbers[$field_id]['states']['stage'] = 'verifying';
            }

        } catch (\Throwable $th) {

            if (config('app.debug')) {
                return $this->addError($model, $th->getMessage());
            } else {
                return $this->addError($model, 'Unexpected error occured. Please try again!');
            }
        }

    }

    /**
     * Change the contact number (and also reset the contact number).
     * 
     * @param string $field_id
     * @return void
     */
    public function change(string $field_id)
    {
        $this->contact_numbers[$field_id]['values']['contact_number'] = '';

        $this->contact_numbers[$field_id]['values']['otp'] = '';

        $this->contact_numbers[$field_id]['states']['stage'] = null;
    }

    /**
     * Remove the contact number (and also reset the contact number).
     * 
     * @param string $field_id
     * @return void
     */
    public function remove(string $field_id)
    {
        $this->change($field_id);

        $this->contact_numbers[$field_id]['states']['visibility'] = false;

        unset($this->contact_numbers[$field_id]['values']['contact_number']);

        unset($this->contact_numbers[$field_id]['values']['otp']);

        $this->showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);
    }

    /**
     * show the available showable first contact number
     * 
     * @return void
     */
    public function add(): void
    {
        $showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);

        if (isset($showable_contact_numbers) && isset($showable_contact_numbers[0])) {

            $this->contact_numbers[$showable_contact_numbers[0]]['states']['visibility'] = true;

            $this->contact_numbers[$showable_contact_numbers[0]]['values']['contact_number'] = '';

            $this->contact_numbers[$showable_contact_numbers[0]]['values']['otp'] = '';
        }

        $this->showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);
    }

    /**
     * array of showable contact numbers where whey have the property => permanent: false
     * 
     * @param array $contact_numbers
     * @return array
     */
    public function listToggableContactNumbers(array $contact_numbers = [])
    {
        return array_values(array_filter(array_map(function ($item) {
            if (!$item['properties']['permanent'] && !$item['states']['visibility']) {
                return $item['id'];
            }
        }, $contact_numbers ?? $this->contact_numbers)));
    }

    /**
     * array of showable contact numbers where whey have the property => permanent: false and state => visibility: false
     * 
     * @param array $contact_numbers
     * @return array
     */
    public function listShowableContactNumbers(array $contact_numbers = [])
    {
        return array_values(array_filter(array_map(function ($item) {
            if (!$item['properties']['permanent'] && !$item['states']['visibility']) {
                return $item['id'];
            }
        }, $contact_numbers ?? $this->contact_numbers)));
    }

    /**
     * array of showable contact numbers where whey have the property => permanent: false and state => visibility: true
     * 
     * @param array $contact_numbers
     * @return array
     */
    public function listHidableContactNumbers(array $contact_numbers = [])
    {
        return array_values(array_filter(array_map(function ($item) {
            if (!$item['properties']['permanent'] && $item['states']['visibility']) {
                return $item['id'];
            }
        }, $contact_numbers ?? $this->contact_numbers)));
    }

    /**
     * Check and modify phone number with country code if not empty.
     *
     * @param string|null $phoneNumber
     * @param string $countryCode
     * @return string|null
     */
    function formatPhoneNumber(?string $phoneNumber, string $countryCode): ?string
    {
        if (!empty($phoneNumber)) {
            // Remove any leading plus sign and replace with country code
            return substr_replace($phoneNumber, $countryCode, 0, 1);
        }
        return null;
    }
}