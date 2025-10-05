<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;

class LoginRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the login process itself
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'mobile' => [
                'required',
                'string',
                'regex:/^[0-9+\-\s()]+$/',
                'min:10',
                'max:20',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.string' => 'Mobile number must be a string.',
            'mobile.regex' => 'Mobile number format is invalid.',
            'mobile.min' => 'Mobile number must be at least 10 characters.',
            'mobile.max' => 'Mobile number may not be greater than 20 characters.',
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.max' => 'Password may not be greater than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'mobile' => 'mobile number',
            'password' => 'password',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            $mobile = $this->input('mobile');

            // Remove any non-digit characters for validation
            $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);

            // Check if mobile number has reasonable length after cleaning
            if (strlen($cleanMobile) < 10) {
                $validator->errors()->add('mobile', 'Mobile number must contain at least 10 digits.');
            }

            if (strlen($cleanMobile) > 15) {
                $validator->errors()->add('mobile', 'Mobile number is too long.');
            }
        });
    }
}
