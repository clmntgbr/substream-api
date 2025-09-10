<?php

namespace App\Enum;

enum VideoFormatEnum: string
{
    case ORIGINAL = 'original';
    case ZOOMED_916 = 'zoomed_916';
    case NORMAL_916_WITH_BORDERS = 'normal_916_with_borders';
    case DUPLICATED_BLURRED_916 = 'duplicated_blurred_916';
}
