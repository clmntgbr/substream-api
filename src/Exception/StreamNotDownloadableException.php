<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class StreamNotDownloadableException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Stream not downloadable', Response::HTTP_BAD_REQUEST);
    }
}
