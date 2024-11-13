<?php

namespace App\Console\Commands;

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

    public function handle(StartScriptDistribution $startScriptDistribution)
    {
        $distributions = Distribution::where('starts_at', '<', now())
            ->where('status', DistributionStatus::PENDING)
            ->get();

        foreach ($distributions as $distribution) {
            if ($distribution->type === DistributionType::SCRIPT) {
                $startScriptDistribution->handle($distribution);
            }
        }
    }
}
