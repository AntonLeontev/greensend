<?php

namespace App\Services\OpenAI\Enums;

enum Model: string
{
    case GPT_3_5_TURBO = 'gpt-3.5-turbo';
    case GPT_4_TURBO = 'gpt-4-turbo';
    case GPT_4 = 'gpt-4';
    case GPT_4O = 'gpt-4o';
    case GPT_4O_MINI = 'gpt-4o-mini';
}
