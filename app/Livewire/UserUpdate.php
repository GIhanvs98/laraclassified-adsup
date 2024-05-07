<?php

namespace App\Livewire;

use Livewire\Component;
use App\Rules\UserUnique;
use App\Traits\ContactNumbersTrait;

class UserUpdate extends Component
{
    use ContactNumbersTrait;

    public $name;

    public $email;

    public function render()
    {
        return view('livewire.user-update');
    }
    public function mount()
    {
        $this->contact_numbers = $this->getContactNumbers();

        $this->contact_numbers['contact_number_1']['rules']['contact_number'][] = 'unique_phone_except_self';

        $user = auth()->user();

        $this->name = $user->name;

        $this->email = $user->email;

        # If want to change the structure or add new contact number please change `contact_numbers` property in `ContactNumbersTrait` or replace `contact_numbers` with same structure.

        if (isset ($this->contact_numbers) && is_array($this->contact_numbers)) {

            $contactNumbers = [
                # 'laravel_model' => 'db_column',
                'contact_number_1' => 'phone_national',
                'contact_number_2' => 'phone_alternate_1',
                'contact_number_3' => 'phone_alternate_2',
                'whatsapp_number' => 'whatsapp_number'
            ];

            foreach ($contactNumbers as $key => $property) {
                if (isset ($user->{$property}) && !empty ($user->{$property})) {
                    $phoneNumber = preg_replace('/\s+/', '', $user->{$property});
                    $phoneNumber = preg_replace('/^\+94/', '0', $phoneNumber);
                    $this->contact_numbers[$key]['values']['contact_number'] = $phoneNumber;

                    $this->contact_numbers[$key]['states']['stage'] = 'success';
                    $this->contact_numbers[$key]['states']['visibility'] = true;
                } else {
                    unset($this->contact_numbers[$key]['values']['contact_number']);
                    $this->contact_numbers[$key]['states']['stage'] = null;
                }
                unset($this->contact_numbers[$key]['values']['otp']);
            }

            $this->showable_contact_numbers = $this->listShowableContactNumbers($this->contact_numbers);
        }

    }

    public function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->contact_numbers as $key => $field) {
            $attributes[$field['models']['contact_number']] = $field['attribute_name'] ?? 'Contact Number';
            $attributes[$field['models']['otp']] = 'OTP';
        }

        return $attributes;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', new UserUnique],
        ];

        foreach ($this->contact_numbers as $key => $field) {
            $rules[$field['models']['contact_number']] = $field['rules']['contact_number'] ?? null;
            $rules[$field['models']['otp']] = $field['rules']['otp'] ?? null;
        }

        return $rules;
    }

    public function update()
    {
        $validated = $this->validate();

        $user = auth()->user();

        $user->name = $validated['name'];

        $user->email = $validated['email'];

        $user->phone_national = $this->contact_numbers['contact_number_1']['values']['contact_number'] ?? null;

        $user->phone = $this->formatPhoneNumber($this->contact_numbers['contact_number_1']['values']['contact_number'] ?? null, config('sms.country-code'));

        $user->phone_alternate_1 = $this->formatPhoneNumber($this->contact_numbers['contact_number_2']['values']['contact_number'] ?? null, config('sms.country-code'));

        $user->phone_alternate_2 = $this->formatPhoneNumber($this->contact_numbers['contact_number_3']['values']['contact_number'] ?? null, config('sms.country-code'));

        $user->whatsapp_number = $this->formatPhoneNumber($this->contact_numbers['whatsapp_number']['values']['contact_number'] ?? null, config('sms.country-code'));

        $user->save();
    }
}
