<?php

namespace Src\ScriptNodes;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class ScriptNode implements ShouldQueue
{
    use Queueable;

    abstract public static function title(): string;

    abstract public static function type(): string;

    public static function serialize(): array
    {
        return [
            'type' => self::type(),
            'class' => self::class,
            'title' => self::title(),
        ];
    }
}
