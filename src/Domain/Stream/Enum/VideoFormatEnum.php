<?php

declare(strict_types=1);

namespace App\Domain\Stream\Enum;

enum VideoFormatEnum: string
{
    case ORIGINAL = 'original';
    case ZOOMED_916 = 'zoomed_916';
    case NORMAL_916_WITH_BORDERS = 'normal_916_with_borders';
}
