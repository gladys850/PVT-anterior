<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CalculatorForm extends FormRequest
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
        return[
            'liquid_calification' => ['required', 'array', 'min:1'],
            'liquid_calification.*.affiliate_id' => ['required', 'integer', 'exists:affiliates,id'],
            'liquid_calification.*.parent_loan_id' => ['integer', 'nullable', 'exists:loans,id'],
            'liquid_calification.*.sismu' => ['boolean', 'nullable'], // en caso de refinanciamiento y/o reprogramación verificación de sismu
            'liquid_calification.*.quota_sismu' => ['nullable', 'required_if:liquid_calification.*.sismu,true'], // en caso de refinanciamiento y/o cuota de sismu
            'liquid_calification.*.contributions' => ['required', 'array', 'min:1'],
            'liquid_calification.*.contributions.*.payable_liquid' => ['required'],
            'liquid_calification.*.contributions.*.border_bonus' => ['required'],
            'liquid_calification.*.contributions.*.position_bonus' => ['required'],
            'liquid_calification.*.contributions.*.public_security_bonus' => ['required'],
            'liquid_calification.*.contributions.*.east_bonus' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'quota_sismu.required_if' => 'La cuota del sismu es requerido',
        ];
    }

}
