<?php

namespace App\Applications\LeaveType\Requests;

use App\Http\Requests\ApiFormRequest;

class LeaveTypeRequest extends ApiFormRequest
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
            'name' => 'required|max:255|min:2',
            'slug' => 'required|max:255|min:2',
            'color' => 'required'
        ];

        return $rules;
    }
    public function messages(){
        return [
            'color.required' => 'users.color.required',
            'name.required' => 'users.name.required',
            'name.max' => 'users.name.max',
            'name.min' => 'users.name.min',
            'slug.required' => 'users.slug.required',
            'slug.max' => 'users.slug.max',
            'slug.min' => 'users.slug.min',
        ];
    }
}
