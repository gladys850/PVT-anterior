<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthForm extends FormRequest
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
		return [
			'username' => 'required|min:4|max:255',
			'password' => 'required|min:4|max:255',
		];
	}

	public function sanitize()
    {
        $input = $this->all();
        if (array_key_exists('username', $input)) $input['username'] = mb_strtolower($input['username']);
        $this->replace($input);
    }
}
