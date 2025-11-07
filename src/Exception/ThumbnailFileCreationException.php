<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class ThumbnailFileCreationException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            'We were unable to create the thumbnail file on the server. Please retry in a moment or use a different image.',
            TranslatableKeyEnum::THUMBNAIL_FILE_CREATION_FAILED->value,
            [],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
