<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpouseResource extends JsonResource
{
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
            'affiliate_id' => $this->affiliate_id,
            'city_identity_card_id' => $this->city_identity_card_id,
            'identity_card' => $this->identity_card,
            'registration' => $this->registration,
            'last_name' => $this->last_name,
            'mothers_last_name' => $this->mothers_last_name,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'surname_husband' => $this->surname_husband,
            'civil_status' => $this->civil_status,
            'birth_date' => $this->birth_date,
            'date_death' => $this->date_death,
            'reason_death' => $this->reason_death,
            'city_birth_id' => $this->city_birth_id,
            'death_certificate_number' => $this->death_certificate_number,
            'due_date' => $this->due_date,
            'is_duedate_undefined' => $this->is_duedate_undefined,
            'official' => $this->official,
            'book' => $this->book,
            'departure' => $this->departure,
            'marriage_date' => $this->marriage_date,
            'civil_status_gender' => $this->civil_status_gender
        ];
    }
}