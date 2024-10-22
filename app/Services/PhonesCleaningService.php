<?php

namespace App\Services;

use App\DTO\CleanedPhonesResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use SplFileObject;

class PhonesCleaningService
{
    public function getHeaders(string $line): array
    {
        $headers = explode(';', $line);
        $headers = Arr::map($headers, static fn ($item) => trim($item));
        $headers = Arr::map($headers, static fn ($item) => trim($item, "\u{FEFF}"));

        return $headers;
    }

    /**
     * Cleans phones and save them to file
     */
    public function clean(string $path): CleanedPhonesResult
    {
        $filePath = Storage::path($path);
        $file = new SplFileObject($filePath, 'r');
        $resultFilePath = 'resultFiles/'.str($path)->afterLast('/')->value();
        $initPhonesCount = 0;
        $resultPhonesCount = 0;

        Storage::put($resultFilePath, '');

        $headers = null;

        while (! $file->eof()) {
            $line = $file->fgets();

            if (str_starts_with($line, 'sep=')) {
                continue;
            }

            if (is_null($headers)) {
                $headers = $this->getHeaders($line);

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

            $phonesCollection = str($lineData['phone'])
                ->trim()
                ->trim('=')
                ->trim('"')
                ->explode(', ');

            $initPhonesCount += $phonesCollection->count();

            $phonesCollection->filter(static function (string $phone) {
                return str_starts_with($phone, '+79') || str_starts_with($phone, '79');
            })
                ->each(function ($phone) use ($resultFilePath, &$resultPhonesCount) {
                    $resultPhonesCount += 1;
                    Storage::append($resultFilePath, trim($phone, '+'));
                });
        }

        return new CleanedPhonesResult($resultFilePath, $initPhonesCount, $resultPhonesCount);
    }
}
