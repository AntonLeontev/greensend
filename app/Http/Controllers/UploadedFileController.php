<?php

namespace App\Http\Controllers;

use App\Enums\UploadedFileStatus;
use App\Http\Requests\UploadedFileStoreRequest;
use App\Models\UploadedFile;
use App\Services\PhonesCleaningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UploadedFileController extends Controller
{
    public function index(): JsonResponse
    {
        $files = UploadedFile::orderByDesc('id')
            ->paginate();

        return response()->json($files);
    }

    public function store(UploadedFileStoreRequest $request, PhonesCleaningService $service): JsonResponse
    {
        $path = $request->file('file')->store('uploadedFiles');

        $result = $service->clean($path);

        $file = UploadedFile::create([
            'path' => $path,
            'label' => $request->get('label'),
            'status' => UploadedFileStatus::CLEANED,
            'result_path' => $result->resultFilePath,
            'initial_phones_number' => $result->initPhonesCount,
            'clean_phones_number' => $result->resultPhonesCount,
        ]);

        return response()->json($file);
    }

    public function destroy(UploadedFile $uploadedFile): void
    {
        Storage::delete($uploadedFile->path);

        if ($uploadedFile->result_path) {
            Storage::delete($uploadedFile->result_path);
        }

        $uploadedFile->delete();
    }

    public function getArchive(UploadedFile $uploadedFile) {}
}
