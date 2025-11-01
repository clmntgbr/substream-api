<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class StreamNotDownloadableException extends BusinessException
{
    public function __construct(string $streamId = '')
    {
        parent::__construct(
            'Stream is not downloadable',
            'error.stream.not_downloadable',
            ['streamId' => $streamId],
            Response::HTTP_FORBIDDEN
        );
    }
}
