<?php

namespace App\Services\OpenAI\Schemas;

use JsonSerializable;

interface ResponseFormat extends JsonSerializable
{
    public function jsonSerialize(): array;
}
