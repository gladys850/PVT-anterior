<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressForm extends FormRequest
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
            'city_address_id' => 'exists:cities,id',
            'zone' =>'nullable|min:3',
            'street' =>'nullable|min:3'
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 1) as $key => $rule) {
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
        if (array_key_exists('zone', $input)) $input['zone'] = mb_strtoupper($input['zone']);
        if (array_key_exists('street', $input)) $input['street'] = mb_strtoupper($input['street']);
        if (array_key_exists('number_address', $input)) $input['number_address'] = mb_strtoupper($input['number_address']);
        $this->replace($input);
    }
}
