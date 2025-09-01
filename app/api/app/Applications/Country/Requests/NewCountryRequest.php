<?php

namespace App\Applications\Country\Requests;

use App\Http\Requests\ApiFormRequest;

class NewCountryRequest extends ApiFormRequest
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
            'country_code' => 'required|max:255|min:2',
        ];

        return $rules;
    }
    public function messages(){
        return [
            'name.required' => 'The Name field is required',
            'country_code.required' => 'The country code field is required',
        ];
    }
}
