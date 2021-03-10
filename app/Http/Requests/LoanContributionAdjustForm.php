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
            'user_id' =>['integer'],
            'type_affiliate'=>['string','in:headline,guarantor,cosigner'],
            'description'=>['string','alpha_spaces','min:3'],
            'period_date'=>['date_format:"Y-m-d"'],
            'loan_id'=>['nullable'],
            'affiliate_id'=>['integer'],
            'adjustable_id'=>['integer'],
            'adjustable_type' => ['string'],
            'amount' =>['integer'],
            'type_adjust'=>['string','in:adjust,liquid'],
          
            
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 4 ) as $key => $rule) {
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
