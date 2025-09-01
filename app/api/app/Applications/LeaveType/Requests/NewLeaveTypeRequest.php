<?php

namespace App\Applications\LeaveType\Requests;

use App\Http\Requests\ApiFormRequest;

class NewLeaveTypeRequest extends ApiFormRequest
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
            'name.required' => 'The name field is required',
            'slug.required' => 'The slug field is required',
            'color.required' => 'The color field is required',
        ];
    }
}
