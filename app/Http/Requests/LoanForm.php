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
            'disbursable_id'=>'integer',
            'disbursable_type'=>'in:affiliates,spouses',
            'procedure_modality_id'=>'exists:procedure_modalities,id',  
            'amount_request'=>'integer',
            'city_id'=>'exists:cities,id',
            'loan_term'=>'integer',
            'disbursement_type_id'=>'exists:payment_types,id',
            'lenders'=>'array|exists:affiliates,id',
            'guarantors'=>'array|exists:affiliates,id',
            'code'=>'nullable', 
            'amount_disbursement'=>'nullable',
            'disbursement_date'=>'nullable|date_format:"Y-m-d"',
            'parent_loan_id'=>'nullable|exists:loans,id',
            'parent_reason'=> 'nullable|min:3',
            'request_date'=>'nullable|date_format:"Y-m-d"',
            'loan_interest_id'=>'nullable|exists:loan_interests,id',
            'loan_state_id'=>'nullable|exists:loan_states,id',
            'amount_aproved'=>'nullable|integer',

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
            //'code' => 'trim|uppercase',
        ];
    }
}
