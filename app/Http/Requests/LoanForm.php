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
use App\Rules\LoanIntervalMaxLender;
use App\Rules\LoanIntervalMaxCosigner;
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
        $hypothecary = false;
        if ($this->procedure_modality_id) {
            $procedure_modality = ProcedureModality::findOrFail($this->procedure_modality_id);
            if($procedure_modality->procedure_type->name == "Préstamo hipotecario") $hypothecary = true;
        } else {
            $procedure_modality = $this->loan->modality;
        }
        $rules = [
            'procedure_modality_id' => ['required','integer', 'exists:procedure_modalities,id'],
            'amount_requested' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'city_id' => ['integer', 'exists:cities,id'],
            'loan_term' => ['integer', 'min:1', 'max:240', new LoanIntervalTerm($procedure_modality)],
            'payment_type_id' => ['integer', 'exists:payment_types,id'],
            'destiny_id' => ['integer', 'exists:loan_destinies,id', new LoanDestiny($procedure_modality)],
            'documents' => ['array', 'min:1', new ProcedureRequirements($procedure_modality)],
            'liquid_qualification_calculated' => ['numeric'],
            'indebtedness_calculated' => ['numeric', 'max:90', new LoanParameterIndebtedness($procedure_modality)],
            'property_id' => ['nullable', 'exists:loan_properties,id'],
            'lenders' => ['array', 'required','min:1', new LoanIntervalMaxLender($procedure_modality)],
            'lenders.*.affiliate_id' => ['required', 'integer', 'exists:affiliates,id'],
            'lenders.*.payment_percentage' => ['required', 'integer'],
            'lenders.*.payable_liquid_calculated' => ['required', 'numeric'],
            'lenders.*.bonus_calculated' => ['required', 'integer'],
            'lenders.*.quota_refinance' => ['required', 'numeric'],
            'lenders.*.indebtedness_calculated' => ['nullable', 'numeric'],
            'lenders.*.liquid_qualification_calculated' => ['required', 'numeric'],
            'guarantors' => ['array',new LoanParameterGuarantor($procedure_modality)],
            'guarantors.*.affiliate_id' => ['required', 'integer', 'exists:affiliates,id'],
            'guarantors.*.payment_percentage' => ['required', 'integer'],
            'guarantors.*.payable_liquid_calculated' => ['required', 'numeric'],
            'guarantors.*.bonus_calculated' => ['required', 'integer'],
            'guarantors.*.quota_refinance' => ['required', 'numeric'],
            'guarantors.*.indebtedness_calculated' => ['nullable', 'numeric'],
            'guarantors.*.liquid_qualification_calculated' => ['required', 'numeric'],
            'personal_references' => ['array',$procedure_modality->loan_modality_parameter->personal_reference? 'required':'nullable' ],
            'consigners' => ['array',new LoanIntervalMaxCosigner($procedure_modality)],
            'documents.*' => ['exists:procedure_documents,id'],
            'disbursable_id' => ['integer'],
            'disbursable_type' => ['string', 'in:affiliates,spouses'],
            'number_payment_type' => ['nullable', 'integer', 'min:6'],
            'disbursement_date' => ['nullable', 'date_format:"Y-m-d"'],
            'parent_loan_id' => ['integer', 'nullable', 'exists:loans,id'],
            'parent_reason'=> ['string', 'nullable', 'in:REFINANCIAMIENTO,REPROGRAMACIÓN'],
            'state_id' => ['exists:loan_states,id'],
            'amount_approved' => ['integer', 'min:200', 'max:700000', new LoanIntervalAmount($procedure_modality)],
            'notes' => ['array'],
            'validated' => ['boolean'],
            'financial_entity_id' => ['nullable', 'integer', 'exists:financial_entities,id']

        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, $hypothecary ? 10 :9 ) as $key => $rule) {
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
