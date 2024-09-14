<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use ZipArchive;

class AppController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            // 'file' => 'required|file|mimetypes:text/csv	|max:6000',
        ]);

        // if ($request->file('file')->isValid()) {}

        $phones = new Collection;
        $headers = null;
        $num = 0;

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', 'uploadedfile.csv');

        $uploadedFile = fopen(storage_path('app/private/'.$filePath), 'r');

        if ($uploadedFile) {
            while (! feof($uploadedFile)) {
                $line = fgets($uploadedFile);
                $num++;

                if (str_starts_with($line, 'sep=')) {
                    continue;
                }

                if (is_null($headers)) {
                    $headers = explode(';', $line);

                    continue;
                }
                $line = explode(';', $line);

                while (count($line) < count($headers)) {
                    $line[] = '';
                }
                while (count($line) > count($headers)) {
                    array_pop($line);
                }

                $lineData = array_combine($headers, $line);

                str($lineData['phone'])
                    ->trim('=')
                    ->trim('"')
                    ->explode(', ')
                    ->filter(static function (string $phone) {
                        return str_starts_with($phone, '+79');
                    })
                    ->each(static fn (string $phone) => $phones->add($phone));
            }
            fclose($uploadedFile);
        } else {
            throw new Exception('File not opened');
        }

        $chunks = $phones->unique()->chunk($request->get('number') - 1);
        $text1 = explode("\n", $request->get('text1'));
        $text2 = explode("\n", $request->get('text2'));
        $text3 = explode("\n", $request->get('text3'));
        $files = collect();

        foreach ($chunks as $key => $chunk) {
            $name = str($originalFileName)->beforeLast('.');

            if (Storage::directoryMissing($name)) {
                Storage::makeDirectory($name);
            }

            $path = storage_path("app/private/$name/{$name}_".($key + 1).'.xlsx');

            $writer = SimpleExcelWriter::create($path);

            foreach ($chunk as $phone) {
                $messages = [trim(Arr::random($text1)), trim(Arr::random($text2)), trim(Arr::random($text3))];

                $writer->addRow([
                    'Телефон' => $phone,
                    'Сообщение' => trim(implode(' ', $messages)),
                ]);
            }
            $files->add($path);
            $writer->close();
        }

        $zipFileName = $originalFileName.'.zip';
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

        Storage::delete($filePath);
        Storage::deleteDirectory(str($originalFileName)->beforeLast('.'));

        // Return the ZIP file as a response
        return response()->download($zipFileName, $zipFileName, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$zipFileName.'"',
        ])->deleteFileAfterSend(true);

        return response()->json('ok');
    }
}
