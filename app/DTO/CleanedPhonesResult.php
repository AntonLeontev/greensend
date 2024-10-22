<?php

namespace App\DTO;

readonly class CleanedPhonesResult
{
    public function __construct(
        public string $resultFilePath,
        public int $initPhonesCount = 0,
        public int $resultPhonesCount = 0,
    ) {}
}
