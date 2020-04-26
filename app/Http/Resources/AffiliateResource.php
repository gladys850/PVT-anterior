<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AffiliateResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->with_category = false;
        if (isset($resource->with_category)) {
            $this->with_category = (bool)$resource->with_category;
        }
        $this->has_loan_role = $this->has_module_permission('prestamos');
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
            'affiliate_state_id' => $this->affiliate_state_id,
            'city_identity_card_id' => $this->city_identity_card_id,
            'city_birth_id' => $this->city_birth_id,
            'degree_id' => $this->degree_id,
            'unit_id' => $this->unit_id,
            'category_id' => $this->category_id,
            'pension_entity_id' => $this->pension_entity_id,
            'identity_card' => $this->identity_card,
            'registration' => $this->registration,
            'type' => $this->type,
            'last_name' => $this->last_name,
            'mothers_last_name' => $this->mothers_last_name,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'surname_husband' => $this->surname_husband,
            'gender' => $this->gender,
            'civil_status' => $this->civil_status,
            'birth_date' => $this->birth_date,
            'date_entry' => $this->date_entry,
            'date_death' => $this->date_death,
            'reason_death' => $this->reason_death,
            'date_derelict' => $this->date_derelict,
            'reason_derelict' => $this->reason_derelict,
            'change_date' => $this->change_date,
            'phone_number' => $this->phone_number,
            'cell_phone_number' => $this->cell_phone_number,
            'afp' => $this->afp,
            'nua' => $this->nua,
            'item' => $this->item,
            'is_duedate_undefined' => $this->is_duedate_undefined,
            'due_date' => $this->due_date,
            'service_years' => $this->service_years,
            'service_months' => $this->service_months,
            'death_certificate_number' => $this->death_certificate_number,
            'affiliate_registration_number' => $this->affiliate_registration_number,
            'file_code' => $this->file_code,
            'picture_saved' => $this->picture_saved,
            'fingerprint_saved' => $this->fingerprint_saved,
            'full_name' => $this->full_name,
            'civil_status_gender' => $this->civil_status_gender,
            'dead' => $this->dead,
            'identity_card_ext' => $this->identity_card_ext,
            'category' => $this->when($this->with_category, $this->category),
            'defaulted' => $this->when($this->has_loan_role, $this->defaulted),
            'cpop' => $this->when($this->has_loan_role, $this->cpop)
        ];
    }

    private function has_module_permission($module)
    {
        return (bool)Auth::user()->roles()->whereHas('module', function($q) use ($module) {
            $q->whereName($module);
        })->count();
    }
}
