<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LoanPaymentRole;

class LoanPaymentsForm extends FormRequest
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
            'ids' => ['required', 'array', 'min:1', 'exists:loan_payments,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id', new LoanPaymentRole($this->ids)]
        ];
    }
}
