<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleSequenceForm extends FormRequest
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
        return [
            'workflow' => 'required|array',
            'workflow.*.role_id' => 'required|exists:roles,id',
            'workflow.*.next_role_id' => 'required|exists:roles,id'
        ];
    }
}
