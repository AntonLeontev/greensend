<?php

namespace App\Enums;

enum UploadedFileStatus: string
{
    case CLEANED = 'cleaned';
    case CLEANING_WHATSAPP = 'cleaning whatsapp';
    case CLEANED_WHATSAPP = 'cleaned whatsapp';
}
