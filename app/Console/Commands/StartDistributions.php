<?php

namespace App\Console\Commands;

use App\Actions\Distributions\StartAiDistribution;
use App\Actions\Distributions\StartScriptDistribution;
use App\Enums\DistributionStatus;
use App\Enums\DistributionType;
use App\Models\Distribution;
use Illuminate\Console\Command;

class StartDistributions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-distributions';

    public function handle(StartScriptDistribution $startScriptDistribution, StartAiDistribution $startAiDistribution)
    {
        $distributions = Distribution::where('starts_at', '<', now())
            ->where('status', DistributionStatus::PENDING)
            ->with('uploadedFile')
            ->get();

        foreach ($distributions as $distribution) {
            if ($distribution->type === DistributionType::SCRIPT) {
                $startScriptDistribution->handle($distribution);
            }

            if ($distribution->type === DistributionType::AI) {
                $startAiDistribution->handle($distribution);
            }
        }
    }
}
