<?php

namespace App\Rules;

use App\User;
use Illuminate\Contracts\Validation\Rule;

class CheckUsernameValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
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
        $total = User::where('email', '=', $this->email)->count();
        if ($total > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.user_email');
    }
}
