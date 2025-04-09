<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class ValidPhoneNumber implements ValidationRule
{
    private PhoneNumberUtil $phoneUtil;

    private string $countryCode;

    public function __construct(string $countryCode = 'US')
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
        $this->countryCode = $countryCode;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $phoneNumber = $this->phoneUtil->parse($value, $this->countryCode);
            if (! $this->phoneUtil->isValidNumber($phoneNumber)) {
                $fail('The phone number is not valid for the selected country.');
            }
        } catch (NumberParseException $e) {
            $fail('The phone number format is invalid.');
        }
    }
}
