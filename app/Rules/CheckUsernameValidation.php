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
    protected $username;

    public function __construct($username)
    {
        $this->username = $username;
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
        $total = User::where('username', '=', $this->username)
            ->orWhere('email', '=', $this->username)->count();
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
        return trans('validation.username');
    }
}
