<?php

namespace App\Http\Requests;
use Waavi\Sanitizer\Laravel\SanitizesInput;
use App\Loan;

use Illuminate\Foundation\Http\FormRequest;

class LoanForm extends FormRequest
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
        $rules = [
            'procedure_modality_id'=>'exists:procedure_modalities,id',
            'request_date'=>'date_format:"Y-m-d"',
            'amount_request'=>'integer',
            'city_id'=>'exists:cities,id',
            'loan_state_id'=>'exists:loan_states,id',
            'code'=>'nullable', 
            'disbursable_id'=>'nullable',
            'disbursable_type'=>'nullable',  
            'amount_disbursement'=>'nullable',
            'parent_loan_id'=>'nullable|exists:loans,id',
            'parent_reason'=> 'nullable|min:3',
            'loan_interest_id'=>'nullable|exists:loan_interests,id',
            'amount_aproved'=>'nullable|integer',
            'loan_term'=>'nullable|integer',
            'disbursement_date'=>'nullable|date_format:"Y-m-d"',
            'disbursement_type_id'=>'nullable|exists:payment_types,id',
            'mofification_date'=>'nullable|date_format:"Y-m-d"'
       ];  

        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 5) as $key => $rule) {
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
            //'code' => 'trim|uppercase',
        ];
    }
}
