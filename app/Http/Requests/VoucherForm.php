<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Voucher;

class VoucherForm extends FormRequest
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
            // 'voucher_type_id' => 'required|exists:voucher_types,id',
            $rules = [
                'payment_type_id' => 'required|exists:payment_types,id',
                'voucher_number' => 'required_if:payment_type_id,1|integer|min:1'
            ];
        return $rules;
    }

    public function messages()
    {
        return [
            //'voucher_type_id.required' => 'El tipo de voucher es requerido',
            'payment_type_id.required' => 'El tipo de pago es requerido',
            'voucher_number.required' => 'El número de voucher es requerido',
            'voucher_number.required_if' => 'El número de voucher es requerido porque se realizó deposito bancario',
        ];
    }
}
