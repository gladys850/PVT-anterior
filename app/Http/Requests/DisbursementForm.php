<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisbursementForm extends FormRequest
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
        return [
            'disbursement_date' => ['required'],
            'payment_type_id' => ['required','exists:payment_types,id'],
            'number_payment_type' => 'required_if:payment_type_id,1|required_if:payment_type_id,2|integer|min:1'
        ];
    }
}
