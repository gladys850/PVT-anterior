<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\ProcedureModality;

class ProcedureRequirements implements Rule
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
        $list = $this->procedure_modality->requirements_list['required'];
        foreach ($list as $key => $group) {
            $list[$key] = $group->pluck('id')->toArray();
        }
        $optional = $this->procedure_modality->requirements_list['optional']->pluck('id');
        $value = collect($value)->diff($optional);
        $list = collect($this->cartesian($list->toArray()));
        foreach ($list as $combination) {
            $combination = collect($combination);
            if ($combination->diff($value)->count() == 0 && $value->diff($combination)->count() == 0) return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Los documentos no corresponden con la modalidad';
    }

    private function cartesian(array $input)
    {
        $result = [[]];
        foreach ($input as $key => $values) {
            $append = [];
            foreach ($values as $value) {
                foreach ($result as $data) {
                    $append[] = $data + [$key => $value];
                }
            }
            $result = $append;
        }
        return $result;
    }
}
