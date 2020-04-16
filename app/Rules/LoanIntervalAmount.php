<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\ProcedureModality;

class LoanIntervalAmount implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(ProcedureModality $procedure_modality)
    {
        $this->procedure_modality = $procedure_modality;
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
        return ($value >= $this->procedure_modality->procedure_type->interval->minimum_amount && $value <= $this->procedure_modality->procedure_type->interval->maximum_amount);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El monto no corresponde con la modalidad';
    }
}
