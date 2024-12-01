<?php

namespace App\Actions\Distributions;

use App\Enums\DistributionStatus;
use App\Enums\DistributionType;
use App\Models\Distribution;
use Illuminate\Support\Facades\Storage;
use Src\ScriptNodes\SendWhatsAppTextMessage;

class StartScriptDistribution
{
    public function handle(Distribution $distribution)
    {
        if ($distribution->type !== DistributionType::SCRIPT) {
            throw new \Exception("Не соответсвует тип рассылки [{$distribution->name}]. Ожидали 'script', получили '{$distribution->type->value}'");
        }

        $file = Storage::get($distribution->uploadedFile->result_path);
        $phones = explode("\n", $file);
        $delay = now()->addSeconds(30);

        foreach ($phones as $phone) {
            dispatch(new SendWhatsAppTextMessage($phone, $distribution->id, 1))->delay($delay);

            $delay = $delay->addSeconds(30);
        }

        $distribution->update([
            'status' => DistributionStatus::PROCESSED,
        ]);
    }
}
