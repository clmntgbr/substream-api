<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class InvalidThumbnailFormatException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            'The provided thumbnail image is not in a supported format. Please upload a JPEG, PNG, GIF, or WEBP file.',
            TranslatableKeyEnum::THUMBNAIL_INVALID_FORMAT->value,
            [],
            Response::HTTP_BAD_REQUEST
        );
    }
}
