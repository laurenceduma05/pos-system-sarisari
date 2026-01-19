<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Members extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'firstname'         => 'required|string|max:255',
            'middlename'        => 'nullable|string|max:255',
            'lastname'          => 'required|string|max:255',
            'gender'            => 'required|in:Male,Female', // adjust if you need more options
            'contact_number'    => 'nullable|string|max:20',
            'address'           => 'required|string|max:255',
            'role'              => 'required|string|max:255',
            'outreach'          => 'required|string|max:255',
        ];
    }

    public function message()
    {
        return [
            'firstname.required' => 'The child’s first name is required.',
            'lastname.required'  => 'The child’s last name is required.',
            'gender.required'    => 'Please select the child’s gender.',
        ];
    }
}
