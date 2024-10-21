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
            'address' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'contact' => ['required', 'string', 'min:10', 'max:15'],
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Max 2MB for avatar
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