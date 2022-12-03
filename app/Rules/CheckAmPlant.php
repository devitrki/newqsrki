<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Plant;

class CheckAmPlant implements Rule
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
        $code = Plant::getCodeById($value);
        // except R100 Head Office Not Validate
        if( $code == 'R100' ){
            return true;
        }

        $am = Plant::getDataAMPlantById($value);

        if (!isset( $am->email )) {
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
        return trans('validation.check_am_plant');
    }
}
