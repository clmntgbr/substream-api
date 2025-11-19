<?php

declare(strict_types=1);

namespace App\Domain\Stream\Enum;

enum LanguageEnum: string
{
    case AUTO = 'auto';
    case ENGLISH = 'english';
    case FRENCH = 'french';
    case GERMAN = 'german';
    case ITALIAN = 'italian';
    case SPANISH = 'spanish';
    case PORTUGUESE = 'portuguese';
    case RUSSIAN = 'russian';
    case JAPANESE = 'japanese';
    case KOREAN = 'korean';
}
