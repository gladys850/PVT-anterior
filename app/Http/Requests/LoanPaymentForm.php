<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Loan;

class LoanPaymentForm extends FormRequest
{
    public function wantsJson()
    {
        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $loan = Loan::findOrFail($this->id);
        if ($loan->disbursement_date) return true;
        abort(403, 'El préstamo aún no ha sido desembolsado');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $loan = Loan::find($this->id);
        $latest_payment = $loan->last_payment;
        if ($latest_payment) {
            $date = $latest_payment->estimated_date;
        } else {
            $date = $loan->disbursement_date;
        }
        $rules = [
            'estimated_date' => 'nullable|date_format:Y-m-d|after:'.$date,
            'estimated_quota' => 'nullable|numeric|min:1'
        ];
        switch ($this->method()) {
            case 'POST': {
                return array_merge($rules, [
                    'payment_type_id' => 'required|exists:payment_types,id',
                    'affiliate_id' => 'nullable|exists:affiliates,id',
                    'voucher_number' => 'nullable|integer|min:1',
                    'receipt_number' => 'nullable|integer|min:1',
                    'description' => 'nullable|string|min:2'
                ]);
            }
            default: {
                return $rules;
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'estimated_date.after_or_equal' => 'La fecha estimada debe ser igual a hoy o posterior'
        ];
    }
}
