<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ClientRequest extends FormRequest
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
            'client_name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
            'client_number' => 'required|integer|regex:/^\d{10}$/',  // if a string consists of exactly 10 digits (0-9) 
            'client_email' => 'required|email|max:255|unique:clients',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'client_number.integer' => 'Client number must be a number',
        ];
    }

    /*
    Assuming that the regular expression regex:/^\d{10}$/ is used to validate a user input, 
    the possible validation errors for this regex could be:

    1. "Value must be 10 digits": This error message is displayed when the user enters a value 
            that is not exactly 10 digits long.
    2. "Value cannot contain non-numeric characters": This error message is displayed when the user 
            enters a value that contains non-numeric characters such as letters, symbols, or spaces.
    3."Value cannot be empty": This error message is displayed when the user leaves the input field 
            blank or enters only white spaces.
    4. "Value must be a string": This error message is displayed when the input value is not a string, 
            such as a number or object.
    */

}
