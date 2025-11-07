<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class StreamNotDownloadableException extends BusinessException
{
    public function __construct(string $streamId = '')
    {
        parent::__construct(
            'Stream is not downloadable',
            TranslatableKeyEnum::STREAM_NOT_DOWNLOADABLE->value,
            ['streamId' => $streamId],
            Response::HTTP_FORBIDDEN
        );
    }
}
