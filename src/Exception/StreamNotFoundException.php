<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class StreamNotFoundException extends BusinessException
{
    public function __construct(string $streamId = '')
    {
        parent::__construct(
            'Stream not found',
            ErrorKeyEnum::STREAM_NOT_FOUND->value,
            ['streamId' => $streamId],
            Response::HTTP_NOT_FOUND
        );
    }
}
