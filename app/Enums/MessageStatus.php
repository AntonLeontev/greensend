<?php

namespace App\Enums;

enum MessageStatus: string
{
    case INIT = 'init';
    case SENT = 'sent';
    case RECEIVED = 'received';
    case VIEWED = 'read';
}
