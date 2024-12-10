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
            'type' => static::type(),
            'class' => static::class,
            'title' => static::title(),
        ];
    }
}
