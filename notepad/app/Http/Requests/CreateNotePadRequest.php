<?php

namespace App\Http\Requests;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // The title is required and must be a string. The maximum length is 100 and cannot be all spaces.
            'title' => ['required', 'string', 'max:100', 'regex:/^\S/'],
            // Content is required to be string.
            'content' => 'required|string',
            ];
    }
}
