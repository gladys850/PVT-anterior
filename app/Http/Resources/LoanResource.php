<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->with_lenders = false;
        if (isset($resource->with_lenders)) {
            $this->with_lenders = (bool)$resource->with_lenders;
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'disbursable_id' => $this->disbursable_id,
            'disbursable_type' => $this->disbursable_type,
            'procedure_modality_id' => $this->procedure_modality_id,
            'parent_loan_id' => $this->parent_loan_id,
            'parent_reason' => $this->parent_reason,
            'request_date' => $this->request_date,
            'amount_requested' => $this->amount_requested,
            'city_id' => $this->city_id,
            'interest_id' => $this->interest_id,
            'state_id' => $this->state_id,
            'amount_approved' => $this->amount_approved,
            'loan_term' => $this->loan_term,
            'disbursement_date' => $this->disbursement_date,
            'payment_type_id' => $this->payment_type_id,
            'modification_date' => $this->modification_date,
            'account_number' => $this->account_number,
            'destiny_id' => $this->destiny_id,
            'personal_reference_id' => $this->personal_reference_id,
            'role_id' => $this->role_id,
            'validated' => $this->validated,
            'balance' => $this->balance,
            'estimated_quota' => $this->estimated_quota,
            'defaulted' => $this->defaulted,
            'lenders' => $this->when($this->with_lenders, $this->lenders),
            'guarantors' => $this->when($this->with_lenders, $this->guarantors)
        ];
    }
}
