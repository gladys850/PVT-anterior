<?php

namespace App\Http\Requests;

use Waavi\Sanitizer\Laravel\SanitizesInput;
use App\Rules\LoanIntervalAmount;
use App\Rules\LoanIntervalTerm;
use App\Rules\LoanDestiny;
use App\Rules\ProcedureRequirements;
use App\ProcedureModality;
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
        $procedure_modality = ProcedureModality::findOrFail($this->procedure_modality_id);
        $rules = [
            'procedure_modality_id' => ['integer', 'exists:procedure_modalities,id'],
            'amount_requested' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'city_id' => ['integer', 'exists:cities,id'],
            'loan_term' => ['integer', 'min:1', 'max:240', new LoanIntervalTerm($procedure_modality)],
            'disbursement_type_id' => ['integer', 'exists:payment_types,id'],
            'lenders' => ['array', 'min:1', 'exists:affiliates,id'],
            'loan_destiny_id' => ['integer', 'exists:loan_destinies,id', new LoanDestiny($procedure_modality)],
            'documents' => ['array', 'min:1', new ProcedureRequirements($procedure_modality)],
            'personal_reference_id' => ['nullable', 'exists:personal_references,id'],
            'documents.*' => ['exists:procedure_documents,id'],
            'guarantors' => ['array', 'exists:affiliates,id'],
            'disbursable_id' => ['integer'],
            'disbursable_type' => ['string', 'in:affiliates,spouses'],
            'account_number' => ['nullable', 'integer', 'min:6'],
            'disbursement_date' => ['nullable', 'date_format:"Y-m-d"'],
            'parent_loan_id' => ['integer', 'nullable', 'exists:loans,id'],
            'parent_reason'=> ['string', 'nullable', 'in:REFINANCIAMIENTO,REPROGRAMACIÓN'],
            'loan_state_id' => ['exists:loan_states,id'],
            'amount_approved' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'notes' => ['array']
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, $procedure_modality->loan_modality_parameter->personal_reference ? 9 : 8) as $key => $rule) {
                    array_push($rules[$key], 'required');
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
            'parent_reason' => 'trim|uppercase'
        ];
    }
}
