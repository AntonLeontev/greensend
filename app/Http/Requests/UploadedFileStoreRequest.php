<?php

namespace App\Http\Requests;

use App\Services\PhonesCleaningService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UploadedFileStoreRequest extends FormRequest
{
    public function __construct(private PhonesCleaningService $service) {}

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
            'file' => ['required', 'file', 'max:5000'],
            'label' => ['required', 'string', 'max:100'],
        ];
    }

    public function passedValidation()
    {
        if ($this->file('file')->getClientOriginalExtension() !== 'csv') {
            throw ValidationException::withMessages(['file' => 'Файл должен быть с расширением csv']);
        }

        $file = $this->file('file')->openFile();
        $headers = null;

        foreach ($file as $lineNumber => $line) {
            if ($lineNumber > 30) {
                return;
            }

            if (str_starts_with($line, 'sep=')) {
                continue;
            }

            $isUtf8 = mb_check_encoding($line, 'UTF-8');

            if (! $isUtf8) {
                throw ValidationException::withMessages(['file' => 'Файл должен быть в кодировке UTF-8']);
            }

            if (is_null($headers)) {
                $headers = $this->service->getHeaders($line);

                if (! in_array('phone', $headers)) {
                    throw ValidationException::withMessages(['file' => 'В файле отсутсвует столбец phone']);
                }
            }
        }
    }
}
