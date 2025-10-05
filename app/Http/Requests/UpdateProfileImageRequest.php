<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class UpdateProfileImageRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by JWT middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'profile_image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:5120', // 5MB max
                File::image()
                    ->min(10) // Minimum 10KB
                    ->max(5120), // Maximum 5MB
                'dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048'
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
            'profile_image.required' => 'Profile image is required.',
            'profile_image.file' => 'The profile image must be a valid file.',
            'profile_image.image' => 'The profile image must be an image file.',
            'profile_image.mimes' => 'The profile image must be a file of type: jpeg, jpg, png, gif, webp.',
            'profile_image.max' => 'The profile image may not be greater than 5MB.',
            'profile_image.min' => 'The profile image must be at least 10KB.',
            'profile_image.dimensions' => 'The profile image must be between 100x100 and 2048x2048 pixels.',
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
            'profile_image' => 'profile image',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            if ($this->hasFile('profile_image')) {
                $file = $this->file('profile_image');

                // Check if file is actually an image
                if (!in_array($file->getMimeType(), [
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ])) {
                    $validator->errors()->add('profile_image', 'The file must be a valid image.');
                }
            }
        });
    }
}
