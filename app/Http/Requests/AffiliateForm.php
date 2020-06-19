<?php

namespace App\Http\Requests;

use Waavi\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class AffiliateForm extends FormRequest
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
            'first_name' => 'alpha_spaces|min:3',
            'gender' => 'in:M,F',
            'birth_date' => 'date_format:"Y-m-d"',
            'city_birth_id' => 'exists:cities,id',
            'city_identity_card_id' => 'exists:cities,id',
            'civil_status' => 'in:C,D,S,V',
            'identity_card' => 'alpha_dash|min:5|max:15',
            'affiliate_state_id' => 'nullable|exists:affiliate_states,id',
            'degree_id' => 'nullable|exists:degrees,id',
            'pension_entity_id' => 'nullable|exists:pension_entities,id',
            'last_name' => 'alpha_spaces|min:3',
            'mothers_last_name' =>'nullable|alpha_spaces|min:3',
            'second_name' =>'nullable|alpha_spaces|min:3',
            'date_death' => 'nullable|date_format:"Y-m-d"',
            'date_entry' => 'nullable|date_format:"Y-m-d"',
            'date_derelict' => 'nullable|date_format:"Y-m-d"',
            'due_date' => 'nullable|date_format:"Y-m-d"',
            'surname_husband' => 'nullable|alpha_spaces|min:3'
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 7) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                $rules['identity_card'] = implode('|', ['unique:affiliates', $rules['identity_card']]);
                $rules['last_name'] = implode('|', ['required_without:mothers_last_name', $rules['last_name']]);
                $rules['mothers_last_name'] = implode('|', ['required_without:last_name', $rules['mothers_last_name']]);
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
            'reason_death' => 'trim|uppercase',
            'identity_card' => 'trim|uppercase',
            'surname_husband' => 'trim|uppercase',
            'gender' => 'trim|uppercase',
            'civil_status' => 'trim|uppercase',
            'cell_phone_number' => 'cast:string'
        ];
    }
}