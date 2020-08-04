<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\RoleSequence;
use App\LoanPayment;

class LoanPaymentRole implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($loan_payment_id)
    {
        if (!is_array($loan_payment_id)) {
            $loan_payment_id = [$loan_payment_id];
        }
        $this->loan_payments = LoanPayment::whereIn('id', $loan_payment_id)->get();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user_roles = Auth::user()->roles->pluck('id');
        foreach ($this->loan_payments as $loan_payment) {
            $roles = RoleSequence::flow($loan_payment->modality->procedure_type->id, $loan_payment->role_id);
            // Derivar sólo trámites pertenecientes al usuario
            if ($user_roles->search($loan_payment->role_id) === false) return false;
            // Derivar a otro rol
            if ($loan_payment->role_id == $value) continue;
            // Derivar si está validado o devolver si no está validado
            if (($loan_payment->validated && $roles->next->search($value) === false) || (!$loan_payment->validated && $roles->previous->search($value) === false)) return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Derivación inválida';
    }
}