<?php

namespace App\Applications\LeaveRequest\Requests;

use App\Http\Requests\ApiFormRequest;

class LeaveRequestRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // we will handle this with middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'request_to' => 'required',
            'leave_type_id' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ];

        return $rules;
    }
    public function messages(){
        return [
            'first_name.required' => 'users.first_name.required',
            'first_name.max' => 'users.first_name.max',
            'first_name.min' => 'users.first_name.min',
            'last_name.required' => 'users.last_name.required',
            'last_name.max' => 'users.last_name.max',
            'last_name.min' => 'users.last_name.min',
            'email.required' => 'users.email.required',
            'email.email' => 'users.email.invalid',
            'email.max' => 'users.email.max',
            'email.min' => 'users.email.min',
            'email.unique' => 'users.email.unique',
            'roles.required' => 'users.roles.required',
            'roles.exists' => 'users.roles.exists',
            'password.required' => 'users.password.required',
            'password.between' => 'users.password.between',
            'password.confirmed' => 'users.password.confirmed',
        ];
    }
}
