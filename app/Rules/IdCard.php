<?php

namespace App\Rules;

use AlicFeng\IdentityCard\InfoHelper;
use Illuminate\Contracts\Validation\Rule;

class IdCard implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return InfoHelper::identityCard()->validate($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '身份证号码错误';
    }
}
