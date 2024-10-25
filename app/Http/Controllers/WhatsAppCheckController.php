<?php

namespace App\Http\Controllers;

use App\Enums\UploadedFileStatus;
use App\Jobs\WhatsappFileCheck;
use App\Models\UploadedFile;
use App\Services\Wamm\WammService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WhatsAppCheckController extends Controller
{
    public function __invoke(Request $request, WammService $wamm): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'starts_with:7'],
        ]);

        $result = $wamm->checkPhone($request->get('phone'));

        return response()->json(['result' => $result]);
    }

    public function checkFile(UploadedFile $uploadedFile): JsonResponse
    {
        if ($uploadedFile->status !== UploadedFileStatus::CLEANED) {
            throw ValidationException::withMessages(['Нельзя повторно запустить проверку файла']);
        }

        $uploadedFile->update([
            'status' => UploadedFileStatus::CLEANING_WHATSAPP,
            'whatsapp_check_percent' => 0,
        ]);

        dispatch(new WhatsappFileCheck($uploadedFile));

        return response()->json($uploadedFile);
    }
}
