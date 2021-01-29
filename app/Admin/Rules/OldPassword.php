<?php
namespace App\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Encore\Admin\Facades\Admin;

use Hash;

class OldPassword implements Rule
{
    private $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // dd(bcrypt($value)."\r\n".Admin::user()->password);
        // return Hash::check($value, Admin::user()->password);
        if(!Hash::check($value, Admin::user()->password)){
            $this->message = '原密码错误';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

}