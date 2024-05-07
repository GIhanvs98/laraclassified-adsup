<?php

namespace App\Traits;

use App\Rules\MaxCharacters;
use App\Rules\MinCharacters;

trait PostAdValidationsTrait
{

    /**
     * CHaracter length of the content typed in description.
     * @var string
     */
    public string $content_length = '0';

    /**
     * Selected images count.
     * @var int
     */
    public int $imagesCount = 0;

    /**
     * Generate attribute names for livewire validation.
     * @return array
     */
    public function validationAttributes(): array
    {
        $attributes = [
            'price.value' => 'price',
            'price.unit' => 'price unit',
            'email.value' => 'email',
            'name.value' => 'name',
        ];

        foreach ($this->adFields as $key => $field) {

            $attributes['fields.' . $field->id] = strtolower($field->name);
        }

        foreach ($this->contact_numbers as $key => $field) {
            $attributes[$field['models']['contact_number']] = $field['attribute_name'] ?? 'Contact Number';

            $otp_verify = option('allow-otp-verification', true) ? 'true' : 'false';

            if (auth()->check()) {

                $otp_verify = auth()->user()->otp_verify;

                if ($otp_verify === "system_default") {
                    $otp_verify = option('allow-otp-verification', true) ? 'true' : 'false';
                }
            }

            if ($otp_verify === 'true') {
                $attributes[$field['models']['otp']] = 'OTP';
            }
        }

        return $attributes;
    }

    /**
     * Update content in when its live updated to filter html tags and show current characters count.
     * @return void
     */
    public function updatedContent(): void
    {
        $contentWithoutTags = strip_tags(html_entity_decode($this->content));

        $this->content = $contentWithoutTags;

        $this->content_length = mb_strlen($contentWithoutTags, 'UTF-8') ?? 0;
    }

    /**
     * Livewire update function before live validation.
     * @param mixed $propertyName
     * @return void
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'images') {
            $this->beforeImagesLiveValidation();
        }

        $this->validateOnly($propertyName);
    }

    /**
     * Images update function before live validation.
     * @return void
     */
    public function beforeImagesLiveValidation()
    {
        $this->imagesCount = count($this->images ?? 0) + count($this->localImages ?? 0);
    }

    /**
     * Generate rules for livewire validation. 
     * @param array $rules
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'min:3', 'max:150'],
            'images' => ['sometimes', 'required', 'array'],
            'images.*' => ['sometimes', 'required', 'mimes:jpeg,jpg,png,jpe', 'max:2048'],
            'imagesCount' => ['required', 'integer', 'min:1', (isset($this->imagesLimit) && !empty($this->imagesLimit)) ? 'max:' . $this->imagesLimit : null],
            'content' => ['required', new MinCharacters(config('content-component.length.min')), new MaxCharacters(config('content-component.length.max'))],
            'price.value' => ['numeric', 'sometimes', 'required'],
            'price.unit' => ['sometimes', 'required'],
            'negotiable' => ['nullable'],
            'email.value' => ['required', 'email:rfc,dns'],
            'name.value' => ['required', 'min:3', 'max:100'],
            'accept_terms' => ['sometimes', 'accepted'],
            'fields.units.*' => ['sometimes', 'required'],
        ];

        foreach ($this->adFields as $key => $field) {

            $rules['fields.' . $field->id] = [
                $field->required ? 'required' : 'nullable',
                (isset($field->max) && !empty($field->max)) ? 'max:' . $field->max : null
            ];
        }

        foreach ($this->contact_numbers as $key => $field) {
            $rules[$field['models']['contact_number']] = $field['rules']['contact_number'] ?? null;

            $otp_verify = option('allow-otp-verification', true) ? 'true' : 'false';

            if (auth()->check()) {

                $otp_verify = auth()->user()->otp_verify;

                if ($otp_verify === "system_default") {
                    $otp_verify = option('allow-otp-verification', true) ? 'true' : 'false';
                }
            }

            if ($otp_verify === 'true') {
                $rules[$field['models']['otp']] = $field['rules']['otp'] ?? null;
            }
        }

        if ($this->action == "edit") {

            if (empty($this->images)) { # This feature only for edit ads.

                unset($rules['images'], $rules['images.*']);
            }
        }

        return $rules;
    }
}