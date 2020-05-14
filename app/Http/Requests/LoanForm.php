<?php

namespace App\Http\Requests;

use Waavi\Sanitizer\Laravel\SanitizesInput;
use App\Rules\LoanIntervalAmount;
use App\Rules\LoanIntervalTerm;
use App\Rules\LoanDestiny;
use App\Rules\LoanRole;
use App\Rules\ProcedureRequirements;
use App\Loan;
use App\ProcedureModality;
use App\Rules\LoanParameterGuarantor;
use App\Rules\LoanParameterIndebtedness;
use Illuminate\Foundation\Http\FormRequest;

class LoanForm extends FormRequest
{
    use SanitizesInput;

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
        if ($this->procedure_modality_id) {
            $procedure_modality = ProcedureModality::findOrFail($this->procedure_modality_id);
        } else {
            $procedure_modality = $this->loan->modality;
        }
        $rules = [
            'procedure_modality_id' => ['integer', 'exists:procedure_modalities,id'],
            'amount_requested' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'city_id' => ['integer', 'exists:cities,id'],
            'loan_term' => ['integer', 'min:1', 'max:240', new LoanIntervalTerm($procedure_modality)],
            'payment_type_id' => ['integer', 'exists:payment_types,id'],
            'lenders' => ['array', 'min:1', 'exists:affiliates,id'],
            'destiny_id' => ['integer', 'exists:loan_destinies,id', new LoanDestiny($procedure_modality)],
            'documents' => ['array', 'min:1', new ProcedureRequirements($procedure_modality)],
            'payable_liquid_calculated' => ['numeric'],
            'bonus_calculated' => ['integer'],
            'liquid_qualification_calculated' => ['numeric'],
            'indebtedness_calculated' => ['numeric', 'max:90', new LoanParameterIndebtedness($procedure_modality)],
            'personal_reference_id' => ['nullable', 'exists:personal_references,id'],
            'guarantors' => ['array', 'exists:affiliates,id',new LoanParameterGuarantor($procedure_modality)],
            'documents.*' => ['exists:procedure_documents,id'],
            'disbursable_id' => ['integer'],
            'disbursable_type' => ['string', 'in:affiliates,spouses'],
            'account_number' => ['nullable', 'integer', 'min:6'],
            'disbursement_date' => ['nullable', 'date_format:"Y-m-d"'],
            'parent_loan_id' => ['integer', 'nullable', 'exists:loans,id'],
            'parent_reason'=> ['string', 'nullable', 'in:REFINANCIAMIENTO,REPROGRAMACIÓN'],
            'state_id' => ['exists:loan_states,id'],
            'amount_approved' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'notes' => ['array'],
            'validated' => ['boolean']
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, $procedure_modality->loan_modality_parameter->personal_reference ? 13 :12 ) as $key => $rule) {
                    array_push($rules[$key], 'required');
                }
                if ($procedure_modality->loan_modality_parameter->guarantors) {
                    array_push($rules['guarantors'], 'required');
                }
                return $rules;
            }
            case 'PUT':
            case 'PATCH':{
                $rules['role_id'] = ['integer', 'exists:roles,id', new LoanRole($this->loan->id)];
                return $rules;
            }
        }
        return $rules;
    }

    public function filters()
    {
        return [
            'parent_reason' => 'trim|uppercase',
            'validated' => 'cast:boolean'
        ];
    }
}
