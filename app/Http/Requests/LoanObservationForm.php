<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use App\Rules\ModuleObservation;
use App\Module;
use Util;

class LoanObservationForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $module = Module::whereName('prestamos')->first();
        if (!$module) abort(404, 'No se encuentra el módulo de préstamos');
        $rules = [
            'observation_type_id' => ['integer', 'exists:observation_types,id', new ModuleObservation($module)],
            'message' => ['string', 'min:1', 'max:255']
        ];
        switch ($this->method()) {
            case 'POST':
                foreach (array_slice($rules, 0, 1) as $key => $rule) {
                    array_push($rules[$key], 'required');
                }
                break;
            case 'PUT':
            case 'PATCH':
                foreach ($rules as $key => $rule) {
                    $rules['update.' . $key] = $rule;
                    $rules['original.' . $key] = array_merge($rule, ['required']);
                    unset($rules[$key]);
                }
                $rules['update.enabled'] = ['boolean'];
                $rules['original.enabled'] = array_merge($rules['update.enabled'], ['required']);
                $rules['original.date'] = ['date', 'required'];
                $rules['original.user_id'] = ['integer', 'exists:users,id', 'required'];
        }
        return $rules;
    }

    public function filters()
    {
        return [
            'observation_type_id' => 'trim',
            'message' => 'trim|uppercase'
        ];
    }
}
