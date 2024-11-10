<?php

namespace App\Enums;

enum ConversationScriptCondition: string
{
    case YES = 'yes';
    case NO = 'no';
    case DEFAULT = 'default';

    public static function toArray(): array
    {
        return [
            [
                'value' => self::YES,
                'label' => 'Положительный ответ',
            ],
            [
                'value' => self::NO,
                'label' => 'Отрицательный ответ',
            ],
            [
                'value' => self::DEFAULT,
                'label' => 'Ответ по-умолчанию',
            ],
        ];
    }
}
