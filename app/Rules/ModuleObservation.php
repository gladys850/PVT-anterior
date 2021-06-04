<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Module;

class ModuleObservation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
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
        return $this->module->observation_types()->pluck('id')->contains($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El tipo de observación no corresponde al módulo ' . $this->module->display_name;
    }
}
