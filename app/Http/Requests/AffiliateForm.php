<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Affiliate;
class AffiliateForm extends FormRequest
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
        $this->sanitize();
        $rules = [
            'first_name' => 'alpha_spaces|min:3',
            'last_name' => 'alpha_spaces|min:3',
            'gender' => '',
            'birth_date' => '',
            'phone_number'=>'integer|nullable',
            'cell_phone_number'=>'integer|nullable',
            'city_birth_id' => '',
            'pension_entity_id' => '',
            'degree_id'=> '',
            'affiliate_state_id'=>'',
            'identity_card' => 'unique:affiliates',
            'city_identity_card_id' => ''
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 12) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
              
            case 'PUT':
            case 'PATCH':{
                return $rules;
            }
        }

    }
    public function sanitize(){
        $input = $this->all();
        if (array_key_exists('first_name', $input)) $input['first_name'] = mb_strtoupper($input['first_name']);
        if (array_key_exists('second_name', $input)) $input['second_name'] = mb_strtoupper($input['second_name']);
        if (array_key_exists('last_name', $input)) $input['last_name'] = mb_strtoupper($input['last_name']);
        if (array_key_exists('mothers_last_name', $input)) $input['mothers_last_name'] = mb_strtoupper($input['mothers_last_name']);
        if (array_key_exists('reason_death', $input)) $input['reason_death'] = mb_strtoupper($input['reason_death']);
        if (array_key_exists('identity_card', $input)) $input['identity_card'] = mb_strtoupper($input['identity_card']);
        $this->replace($input);
    } 
}





