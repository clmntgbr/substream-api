<?php

namespace App\Exception;

class UnauthorizedHttpException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Unauthorized', 400);
    }
}
