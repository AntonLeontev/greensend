<?php

namespace App\Http\Controllers;

use App\Services\Wamm\Exceptions\WammException;
use App\Services\Wamm\WammService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use ZipArchive;

class AppController extends Controller
{
    public function handle(Request $request, WammService $wamm)
    {
        set_time_limit(60 * 30);

        $request->validate([
            'file' => 'required|file|max:15000',
        ]);

        // if ($request->file('file')->isValid()) {}

        $phones = new Collection;
        $headers = null;
        $num = 0;

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();

        if (str($originalFileName)->afterLast('.')->value() !== 'csv') {
            abort(422, 'Файл должен быть csv');
        }

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
                    $headers = Arr::map($headers, static fn ($item) => trim($item));
                    $headers = Arr::map($headers, static fn ($item) => trim($item, "\u{FEFF}"));

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
                    ->trim()
                    ->trim('=')
                    ->trim('"')
                    ->explode(', ')
                    ->filter(static function (string $phone) {
                        return str_starts_with($phone, '+79') || str_starts_with($phone, '79');
                    })
                    ->each(static function (string $phone) use ($phones, $wamm) {
                        try {
                            if ($wamm->checkPhone($phone)) {
                                $phones->add(trim($phone, '+'));
                            }
                        } catch (WammException $e) {
                            // do nothing
                        }
                    });
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

        Storage::delete($filePath);
        Storage::deleteDirectory(str($originalFileName)->beforeLast('.'));

        // Return the ZIP file as a response
        $name = str($zipFileName)->afterLast('/')->value();

        return response()->download($zipFileName, $name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ])->deleteFileAfterSend(true);

        return response()->json('ok');
    }

    public function handle2(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:15000',
        ]);

        // if ($request->file('file')->isValid()) {}

        $phones = new Collection;
        $headers = null;
        $num = 0;

        $file = $request->file('file');
        $originalFileName = $file->getClientOriginalName();

        if (str($originalFileName)->afterLast('.')->value() !== 'csv') {
            abort(422, 'Файл должен быть csv');
        }

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
                    $headers = Arr::map($headers, static fn ($item) => trim($item));
                    $headers = Arr::map($headers, static fn ($item) => trim($item, "\u{FEFF}"));

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
                    ->trim()
                    ->trim('=')
                    ->trim('"')
                    ->explode(', ')
                    ->filter(static function (string $phone) {
                        return str_starts_with($phone, '+79') || str_starts_with($phone, '79');
                    })
                    ->each(static function (string $phone) use ($phones) {
                        $phones->add(trim($phone, '+'));
                    });
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

        Storage::delete($filePath);
        Storage::deleteDirectory(str($originalFileName)->beforeLast('.'));

        // Return the ZIP file as a response
        $name = str($zipFileName)->afterLast('/')->value();

        return response()->download($zipFileName, $name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ])->deleteFileAfterSend(true);

        return response()->json('ok');
    }
}
