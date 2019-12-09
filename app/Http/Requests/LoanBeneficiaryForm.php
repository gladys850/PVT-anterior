<?php

namespace App\Http\Requests;

use Waavi\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use App\LoanBeneficiary;

class LoanBeneficiaryForm extends FormRequest
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
