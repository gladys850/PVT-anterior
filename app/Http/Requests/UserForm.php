<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserForm extends FormRequest
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
            'username' => 'string|min:3|unique:users,username',
            'password' => 'string|min:5',
            'position' => 'string',
            'active' => 'boolean'
        ];
        switch ($this->method()) {
            case 'POST': {
                foreach (array_slice($rules, 0, 4) as $key => $rule) {
                    $rules[$key] = implode('|', ['required', $rule]);
                }
                return $rules;
            }
            case 'PUT':
            case 'PATCH': {
                return $rules;
            }
        }
    }

    public function sanitize()
    {
        $input = $this->all();
        if (array_key_exists('username', $input)) $input['username'] = mb_strtolower($input['username']);
        if (array_key_exists('first_name', $input)) $input['first_name'] = mb_strtoupper($input['first_name']);
        if (array_key_exists('position', $input)) $input['position'] = mb_strtoupper($input['position']);
        if (array_key_exists('last_name', $input)) $input['last_name'] = mb_strtoupper($input['last_name']);
        if (array_key_exists('password', $input)) $input['password'] = Hash::make($input['password']);
        $this->replace($input);
    }
}
