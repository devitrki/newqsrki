<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;

use App\Models\Plant;
use App\Models\Company;

class CheckAmPlant implements Rule
{
    protected $companyId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
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
        $plantCodeHO = Company::getConfigByKey($this->companyId, 'PLANT_CODE_HO');
        if (!$plantCodeHO || $plantCodeHO == '') {
            return false;
        }

        $plantCodeHO = explode(',', $plantCodeHO);

        if( in_array($code, $plantCodeHO) ){
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
