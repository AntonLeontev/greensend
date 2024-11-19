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
            'name' => ['required', 'string', 'max:250'],
            'uploaded_file_id' => ['required', 'integer', 'exists:uploaded_files,id'],
            'type' => ['required', 'string', new Enum(DistributionType::class)],
            'channel_id' => ['required', 'integer', 'exists:channels,id'],

            'start_date' => ['sometimes', 'required_with:start_time', 'date'],
            'start_time' => ['sometimes', 'required_with:start_date', 'date_format:H:i'],

            'conversation' => ['required_if:type,script', 'json'],

            'first_message' => ['required_if:type,ai', 'string', 'max:1000'],
            'system_message' => ['required_if:type,ai', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'start_date' => 'Дата отложенного запуска',
            'start_time' => 'Время отложенного запуска',
            'channel_id' => 'Телефон отправки',
            'first_message' => 'Первое сообщение в рассылке',
        ];
    }
}
