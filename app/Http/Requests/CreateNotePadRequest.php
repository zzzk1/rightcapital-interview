<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateNotePadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // The title is required and must be a string. The maximum length is 100 and cannot be all spaces.
            'title' => ['required', 'string', 'max:100', 'regex:/^\S/'],
            // Content is not required, but must be a string if provided.
            'content' => 'string',
            ];
    }
}
