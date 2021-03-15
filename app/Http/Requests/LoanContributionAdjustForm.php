<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanContributionAdjustForm extends FormRequest
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
            'type_affiliate'=>['string','in:lender,guarantor,cosigner'],
            'description'=>['string','alpha_spaces','min:3'],
            'period_date'=>['date_format:"Y-m-d"'],
            'loan_id'=>['integer','nullable','exists:loans,id'],
            'affiliate_id'=>['integer','exists:affiliates,id'],
            'adjustable_id'=>['integer'],
            'adjustable_type' => ['string'],
            'amount' =>['numeric'],
            'type_adjust'=>['string','in:adjust,liquid'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],        
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 3) as $key => $rule) {
                    array_push($rules[$key], 'required');
                }
            }
            case 'PUT':
            case 'PATCH':{
                return $rules;
            }
        }
        return $rules;

       
    }
}
