<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Controllers\Api\V1\StatisticController;

class StatisticFilter implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($module)
    {
        $controller = new StatisticController();
        $this->filters = in_array($module, array_keys($controller->get_filters())) ? array_keys($controller->get_filters()[$module]) : [];
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
        return in_array($value, $this->filters);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El filtro no corresponde con la modalidad';
    }
}
