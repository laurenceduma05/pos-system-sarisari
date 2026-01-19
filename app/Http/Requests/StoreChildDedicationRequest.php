<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildDedicationRequest extends FormRequest
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
    public function rules() : array
    {
        return [
            'child_firstname'     => 'required|string|max:255',
            'child_middlename'    => 'nullable|string|max:255',
            'child_lastname'      => 'required|string|max:255',
            'child_dob'           => 'required|date',
            'child_pob'           => 'nullable|string|max:255',
            'child_gender'        => 'required|in:Male,Female', // adjust if you need more options

            'father_fullname'     => 'required|string|max:255',
            'mother_fullname'     => 'required|string|max:255',
            'contact_number'      => 'nullable|string|max:20',
            'address'             => 'required|string|max:255',

            'date_of_dedication'  => 'required|date',
            'venue'               => 'required|string|max:255',
            'officiating_pastor'  => 'required|string|max:255',

            'sponsors'            => 'required|array|min:1',
            // 'sponsors.*'          => 'required|array|max:255',
        ];
    }

    public function message()
    {
        return [
            'child_firstname.required' => 'The child’s first name is required.',
            'child_lastname.required'  => 'The child’s last name is required.',
            'child_dob.required'       => 'Please provide the child’s date of birth.',
            'child_gender.required'    => 'Please select the child’s gender.',

            'father_fullname.required' => 'The father’s full name is required.',
            'mother_fullname.required' => 'The mother’s full name is required.',

            'date_of_dedication.required' => 'Please select the date of dedication.',
            'venue.required'              => 'The venue is required.',
            'officiating_pastor.required' => 'Please provide the officiating pastor’s name.',

            'sponsors.required'   => 'At least one sponsor is required.',
            'sponsors.*.required' => 'Each sponsor name must be filled out.',
        ];
    }
}
