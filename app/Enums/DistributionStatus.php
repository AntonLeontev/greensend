<?php

namespace App\Enums;

enum DistributionStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
}
