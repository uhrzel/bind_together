<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable',],
            'contact' => ['nullable', 'string', 'min:10', 'max:15'],
            'old_password' => ['required_with:new_password'], // Old password required only if changing password
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'], // Ensure new password follows the same rules
            'confirm_password' => ['nullable', 'string', 'min:8'], // Confirm password rule
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10248'], // Max 2MB for avatar
            'suffix' => ['nullable', 'string', 'max:10'], // Suffix is optional
            'campus_id' => ['nullable', 'exists:campuses,id'], // Ensure campus exists
            'program_id' => ['nullable', 'exists:programs,id'], // Ensure program exists
            'year_level' => ['nullable', 'string',], // Valid year levels
        ];
    }

    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Remove password from the request if it's null or not provided
        if ($this->password == null) {
            $this->request->remove('password');
        }
    }
}