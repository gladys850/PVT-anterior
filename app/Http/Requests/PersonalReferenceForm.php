<?php

namespace App\Http\Requests;

use Waavi\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use App\PersonalReference;

class PersonalReferenceForm extends FormRequest
{
    use SanitizesInput;

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
        $rules = [
            'city_identity_card_id'=>'integer|exists:cities,id',
            'identity_card'=>'string|min:3',
            'last_name'=>'string|alpha_spaces|min:3',
            'first_name'=>'string|alpha_spaces|min:3',
            'mothers_last_name'=>'string|nullable|alpha_spaces|min:3',
            'second_name'=>'string|nullable|alpha_spaces|min:3',
            'surname_husband'=>'string|nullable|alpha_spaces|min:3',
            'phone_number'=>'nullable',
            'cell_phone_number'=>'nullable'
        ];

        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 4) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
            case 'PUT':
            case 'PATCH':{
                return $rules;
            }
        }
        return $rules;
    }

    public function filters()
    {
        return [
            'first_name' => 'trim|uppercase',
            'second_name' => 'trim|uppercase',
            'last_name' => 'trim|uppercase',
            'mothers_last_name' => 'trim|uppercase',
            'identity_card' => 'trim|uppercase',
            'surname_husband' => 'trim|uppercase',
            'gender' => 'trim|uppercase',
            'civil_status' => 'trim|uppercase'
        ];
    }
}
