<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AidContributionForm extends FormRequest
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
            'affiliate_id' => 'nullable|exists:affiliates,id',
            'month_year' => 'nullable|date_format:"Y-m-d"',
            'rent' =>'nullable|numeric',
            'dignity_rent'=>'nullable|numeric|max:2000',
            'quotable' =>'nullable|numeric',
            'user_id' => 'nullable|exists:users,id',
            'type' => 'nullable|alpha_spaces|min:3|in:PLANILLA',
            'interest'=>'nullable|numeric|max:90',
            'total'=>'nullable|numeric|max:90',
            'affiliate_contribution' =>'nullable|boolean',
            'mortuary_aid' => 'nullable|numeric|max:90',
            'valid' =>'nullable|boolean'
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 4) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
            //case 'GET':
            case 'PUT':
            case 'PATCH':{
                return $rules;
            }
        }
    }

    public function filters()
    {
        return [
            'type' => 'trim|uppercase',
        ];
    }
}
