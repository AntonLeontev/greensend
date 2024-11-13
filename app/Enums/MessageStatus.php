<?php

namespace App\Enums;

enum MessageStatus: string
{
    case INIT = 'init';
    case SENT = 'sent';
    case RECEIVED = 'received';
    case DELIVERED = 'delivered';
    case VIEWED = 'viewed';
}
