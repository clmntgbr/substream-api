<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class StreamNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Stream not found', Response::HTTP_BAD_REQUEST);
    }
}
