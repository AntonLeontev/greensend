<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetArchiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text1' => ['required', 'string', 'max:500'],
            'text2' => ['nullable', 'string', 'max:500'],
            'text3' => ['nullable', 'string', 'max:500'],
            'number' => ['required', 'integer', 'min:1'],
        ];
    }
}
