<?php

namespace App\Http\Requests;

use App\Enums\DistributionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class DistributionStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uploaded_file_id' => ['required', 'integer', 'exists:uploaded_files,id'],
            'type' => ['required', 'string', new Enum(DistributionType::class)],
            'channel_id' => ['required', 'integer', 'exists:channels,id'],
            'start_date' => ['sometimes', 'required_with:start_time', 'date'],
            'start_time' => ['sometimes', 'required_with:start_date', 'date_format:H:i'],
            'conversation' => ['required', 'json'],
        ];
    }
}
