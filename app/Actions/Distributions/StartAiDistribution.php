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

        foreach ($phones as $phone) {
            dispatch(new SendAiDistributionInitMessage($phone, $distribution->id));
        }

        $distribution->update([
            'status' => DistributionStatus::PROCESSED,
        ]);
    }
}
