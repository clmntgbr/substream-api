<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class StreamNotFoundException extends BusinessException
{
    public function __construct(string $streamId = '')
    {
        parent::__construct(
            'Stream not found',
            'error.stream.not_found',
            ['streamId' => $streamId],
            Response::HTTP_NOT_FOUND
        );
    }
}
