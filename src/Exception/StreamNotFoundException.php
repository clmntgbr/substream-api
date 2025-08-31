<?php

namespace App\Exception;

class StreamNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Stream not found', 400);
    }
}
