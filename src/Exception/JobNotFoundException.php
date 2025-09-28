<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class JobNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Job not found', Response::HTTP_BAD_REQUEST);
    }
}
