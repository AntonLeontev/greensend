<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChannelCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => ['required', 'string', 'unique:channels,number', 'starts_with:7'],
            'token' => ['required', 'string', 'max:250'],
            'label' => ['nullable', 'string', 'max:250'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'number' => preg_replace('/\D/', '', $this->number),
        ]);
    }
}
