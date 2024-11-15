<?php

namespace App\Http\Controllers;

use App\Enums\UploadedFileStatus;
use App\Http\Requests\GetArchiveRequest;
use App\Http\Requests\UploadedFileStoreRequest;
use App\Models\UploadedFile;
use App\Services\PhonesCleaningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use ZipArchive;

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

    public function getArchive(UploadedFile $uploadedFile, GetArchiveRequest $request)
    {
        $file = Storage::get($uploadedFile->result_path);

        $phones = collect(explode("\n", $file));

        $chunks = $phones->chunk($request->get('number'));

        $text1 = explode("\n", $request->get('text1'));
        $text2 = explode("\n", $request->get('text2'));
        $text3 = explode("\n", $request->get('text3'));
        $files = collect();

        $name = $uploadedFile->label;
        foreach ($chunks as $key => $chunk) {

            if (Storage::disk('local')->directoryMissing($name)) {
                Storage::disk('local')->makeDirectory($name);
            }

            $path = storage_path("app/private/$name/{$name}_".($key + 1).'.xlsx');

            $writer = SimpleExcelWriter::create($path);

            foreach ($chunk as $phone) {
                if (empty($phone)) {
                    continue;
                }

                $messages = [trim(Arr::random($text1)), trim(Arr::random($text2)), trim(Arr::random($text3))];

                $writer->addRow([
                    'Телефон' => $phone,
                    'Сообщение' => trim(implode(' ', $messages)),
                ]);
            }
            $files->add($path);
            $writer->close();
        }

        $zipFileName = storage_path("app/private/{$name}.zip");
        $zip = new ZipArchive;
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $fileName = basename($file);
                $zip->addFile($file, $fileName);
            }
            $zip->close();
        }

        foreach ($files as $file) {
            unlink($file);
        }

        Storage::disk('local')->deleteDirectory($name);

        // Return the ZIP file as a response
        $name = str($zipFileName)->afterLast('/')->value();

        return response()->download($zipFileName, $name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ])->deleteFileAfterSend(false);
    }
}
