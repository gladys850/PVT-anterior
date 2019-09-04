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
   * Validate request
   * @return
   */
  public function validate()
  {
    switch ($this->method()) {
      case 'POST': {
          if (User::whereUsername($this->input('username'))->count() == 0) return parent::validate();
        }
      case 'PUT':
      case 'PATCH': {
          if (User::find($this->input('id'))) return parent::validate();
        }
      default: {
          return abort(404);
        }
    }
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
      'city_id' => 'exists:cities,id',
      'first_name' => 'alpha|min:3',
      'last_name' => 'alpha|min:3',
      'phone' => 'integer|min:7|max:8',
      'username' => 'string|min:3|unique:users,username',
      'password' => 'string|min:5',
      'position' => 'nullable|string',
      'gender' => 'nullable|in:M,F'
    ];

    switch ($this->method()) {
      case 'POST': {
          foreach (array_slice($rules, 0, 6) as $key => $rule) {
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

  public function messages()
  {
    return [
      'required' => 'El campo es requerido',
      'alpha' => 'El campo solo puede contener letras',
      'string' => 'El campo solo puede contener letras y números',
      'exists' => 'El registro no existe',
      'unique' => 'El registro ya existe',
      'in' => 'Opción inválida',
      'first_name.min' => 'El nombre debe contener al menos 3 caracteres',
      'last_name.min' => 'El apellido debe contener al menos 3 caracteres',
      'username.min' => 'El nombre de usuario debe contener al menos 3 caracteres',
      'password.min' => 'La contraseña debe contener al menos 3 caracteres',
      'phone.min' => 'El teléfono debe contener al menos 7 caracteres',
      'phone.max' => 'El teléfono puede contener máximo 8 caracteres'
    ];
  }

  public function sanitize()
  {
    $input = $this->all();

    if (array_key_exists('gender', $input)) $input['gender'] = mb_strtoupper($input['gender']);
    if (array_key_exists('username', $input)) $input['username'] = mb_strtolower($input['username']);
    if (array_key_exists('first_name', $input)) $input['first_name'] = mb_strtoupper($input['first_name']);
    if (array_key_exists('last_name', $input)) $input['last_name'] = mb_strtoupper($input['last_name']);
    if (array_key_exists('position', $input)) $input['position'] = mb_strtoupper($input['position']);
    if (array_key_exists('password', $input)) $input['password'] = Hash::make($input['password']);

    $this->replace($input);
  }
}
