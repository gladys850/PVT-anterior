<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Affiliate;
class AffiliateForm extends FormRequest
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
            'identity_card' => 'required|unique:affiliates',
            'city_identity_card_id' => 'required|min:1',
            'first_name' => 'required|min:1',
            'gender' => 'required',
            'birth_date' => 'required',
            'category_id' => 'required',
            'degree_id' => 'required',
            'last_name' => '',
            'mothers_last_name' => '',
            'city_birth_id' => 'required',
        ];
    }
    public function messages(){
        return [
            'identity_card.required' => 'Se requiere llenar este campo',
            'identity_card.unique' => 'El carnet introducido ya existe',
            'city_identity_card_id.required' => 'Debe seleccionar una opción',
            'first_name.required' => 'Se requiere llenar este campo',
            'gender.required' => 'Debe seleccionar una opción',
            'birth_date.required' => 'Debe seleccionar una opción',
            'category_id' => 'Debe seleccionar una opción',
            'degree_id' => 'Debe seleccionar una opción',
            'nua.integer' => 'Debe introducir solo números o cero'
        ];
    }
    public function sanitize(){
        $input = $this->all();
        $input['gender'] = mb_strtoupper($input['gender']);
		$input['first_name'] = mb_strtoupper($input['first_name']);
		if (array_key_exists('second_name', $input)) {
			$input['second_name'] = mb_strtoupper($input['second_name']);
		}
		if (array_key_exists('last_name', $input)) {
			$input['last_name'] = mb_strtoupper($input['last_name']);
		}
		if (array_key_exists('mothers_last_name', $input)) {
			$input['mothers_last_name'] = mb_strtoupper($input['mothers_last_name']);
		}

		$this->replace($input);

    } 
}





