<?php

namespace App\Actions\Distributions;

use App\Enums\DistributionStatus;
use App\Enums\DistributionType;
use App\Jobs\SendAiDistributionInitMessage;
use App\Models\Distribution;
use Illuminate\Support\Facades\Storage;

class StartAiDistribution
{
    public function handle(Distribution $distribution)
    {
        if ($distribution->type !== DistributionType::AI) {
            throw new \Exception("Не соответсвует тип рассылки [{$distribution->name}]. Ожидали 'ai', получили '{$distribution->type->value}'");
        }

        $file = Storage::get($distribution->uploadedFile->result_path);
        $phones = explode("\n", $file);
        $delay = now()->addSeconds(30);

        foreach ($phones as $phone) {
            dispatch(new SendAiDistributionInitMessage($phone, $distribution->id))->delay($delay);

            $delay = $delay->addSeconds(30);
        }

        $distribution->update([
            'status' => DistributionStatus::PROCESSED,
        ]);
    }
}
