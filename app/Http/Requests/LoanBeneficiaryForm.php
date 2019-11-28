<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\LoanBeneficiary;

class LoanBeneficiaryForm extends FormRequest
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
    {   $this->sanitize();
        $rules = [
            'city_identity_card_id'=>'exists:cities,id', 
            'identity_card'=>'min:3|unique:loan_beneficiaries',
            'last_name'=>'alpha_spaces|min:3', 
            'mothers_last_name'=>'nullable|alpha_spaces|min:3',
            'first_name'=>'alpha_spaces|min:3',
            'birth_date'=>'date_format:"Y-m-d"',
            'gender'=> 'in:M,F',
            'civil_status'=>'in:C,D,S,V',
            'second_name'=>'nullable|alpha_spaces|min:3',
            'surname_husband'=>'nullable|alpha_spaces|min:3',
            'phone_number'=>'nullable',
            'cell_phone_number'=>'nullable'
       ];  

        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 8) as $key => $rule) {
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
    public function sanitize(){
        $input = $this->all();
        if (array_key_exists('first_name', $input)) $input['first_name'] = mb_strtoupper($input['first_name']);
        if (array_key_exists('second_name', $input)) $input['second_name'] = mb_strtoupper($input['second_name']);
        if (array_key_exists('last_name', $input)) $input['last_name'] = mb_strtoupper($input['last_name']);
        if (array_key_exists('mothers_last_name', $input)) $input['mothers_last_name'] = mb_strtoupper($input['mothers_last_name']);
        if (array_key_exists('identity_card', $input)) $input['identity_card'] = mb_strtoupper($input['identity_card']);
        if (array_key_exists('surname_husband', $input)) $input['surname_husband'] = mb_strtoupper($input['surname_husband']);
        if (array_key_exists('gender', $input)) $input['gender'] = mb_strtoupper($input['gender']);
        if (array_key_exists('civil_status', $input)) $input['civil_status'] = mb_strtoupper($input['civil_status']);

        $this->replace($input);
    }
}
