<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpouseForm extends FormRequest
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
        $this->sanitize();
        $rules = [
            'first_name' => 'alpha_spaces|min:3',
            'last_name' => 'alpha_spaces|min:3', 
            'city_identity_card_id' => 'exists:cities,id',
            'birth_date' => 'date_format:"Y-m-d"',
            'city_birth_id' => 'exists:cities,id', 
            'affiliate_id' => 'exists:affiliates,id', 
            'identity_card' => 'min:3',
            'civil_status' => 'in:C,D,S,V',
            'second_name' =>'nullable|alpha_spaces|min:3',
            'mothers_last_name' =>'nullable|alpha_spaces|min:3',
            'due_date' => 'nullable|date_format:"Y-m-d"',
            'marriage_date' => 'nullable|date_format:"Y-m-d"',
            'surname_husband' => 'nullable|min:3',
            'date_death' => 'nullable|date_format:"Y-m-d"',
            'reason_death' => 'nullable|min:3',
            'death_certificate_number' => 'nullable|min:3',
            'due_date' => 'nullable|date_format:"Y-m-d"'
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 8) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                $rules['identity_card'] = implode('|', ['unique:spouses', $rules['identity_card']]);
                return $rules;
            }
              
            case 'PUT':
            case 'PATCH':{
                return $rules;
            }
        }
    }
    public function sanitize(){
        $input = $this->all();
        if (array_key_exists('first_name', $input)) $input['first_name'] = mb_strtoupper($input['first_name']);
        if (array_key_exists('last_name', $input)) $input['last_name'] = mb_strtoupper($input['last_name']);
        if (array_key_exists('second_name', $input)) $input['second_name'] = mb_strtoupper($input['second_name']);
        if (array_key_exists('mothers_last_name', $input)) $input['mothers_last_name'] = mb_strtoupper($input['mothers_last_name']);
        if (array_key_exists('surname_husband', $input)) $input['surname_husband'] = mb_strtoupper($input['surname_husband']);
        if (array_key_exists('reason_death', $input)) $input['reason_death'] = mb_strtoupper($input['reason_death']);
        if (array_key_exists('identity_card', $input)) $input['identity_card'] = mb_strtoupper($input['identity_card']);
        $this->replace($input);
    }
    public function messages(){
        return[
            'first_name.required' => 'El campo Primer Nombre del Conyugue es requerido',
            'first_name.alpha_spaces' => 'El campo Primer Nombre del Conyugue solo puede contener letras y espacios.',
            'first_name.min' =>'El campo Primer Nombre del Conyugue debe ser de al menos :min',
            'last_name.required' => 'El campo Apellido Paterno del Conyugue es requerido',
            'last_name.alpha_spaces' => 'El campo Apellido Paterno del Conyugue solo puede contener letras y espacios.',
            'last_name.min' =>'El campo Apellido Paterno del Conyugue debe ser de al menos :min',
            'city_identity_card_id.required' => 'El campo Ciudad de expedición del Conyugue es requerido',
            'city_identity_card_id.exists' =>'La selección Ciudad de expedición del Conyugue es inválida',
            'birth_date.required' =>'Elcampo Fecha de nacimiento del Conyugue es requerido',
            'birth_date.date_format' =>'El campo fecha de nacimiento del Conyugue no coincide con el formato :format.',
            'city_birth_id.required' => 'El campo Ciudad de nacimiento del Conyugue es requerido',
            'city_birth_id.exists' =>'La selección Ciudad de nacimiento del Conyugue es inválida.', 
            'identity_card.min' =>'El campo Carnet de Identidad del Conyugue debe ser de almenos :min',
            'identity_card.required' => 'El campo Carnet de Identidad del Conyugue es requerido',
            'identity_card.unique' =>'El campo Carnet de Identidad del Conyugue ya existe.',
            'civil_status.required' =>'El campo Estado Civil del Conyugue es requerido',
            'civil_status.in' =>'La selección Estado Civil del Conyugue es inválida',
            'second_name.alpha_spaces' =>'El campo Segundo Nombre del Conyugue solo puede contener letras y espacios.',
            'second_name.min' =>'El campo Segundo Nombre del Conyugue debe ser de al menos :min',
            'mothers_last_name.alpha_spaces' =>'El campo Apellido Materno del Conyugue solo puede contener letras y espacios.',
            'mothers_last_name.min' =>'El campo Apellido Materno del Conyugue debe ser de al menos :min',
            'due_date.date_format' =>'El campo Fecha de vencimiento de CI del Conyugue no coincide con el formato :format.',
            'marriage_date.date_format' =>'El campo Fecha de casamiento del Conyugue no coincide con el formato :format.',
            'surname_husband.min' =>'El campo Apellido del Esposo del Conyugue debe ser de al menos :min',
            'date_death.date_format' =>'El campo fecha de fallecimiento del Conyugue no coincide con el formato :format.',
            'reason_death.min' => 'El campo Motivo de Fallecimiento del Conyugue debe ser de al menos :min',
            'death_certificate_number.min' =>'El campo Número de certificado de defunción del Conyugue debe ser de al menos :min',
            'due_date.date_format' =>'El campo fecha de vencimiento de CI del Conyugue no coincide con el formato :format.',

        ];
    }
}
