<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class OptionNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Option not found', Response::HTTP_BAD_REQUEST);
    }
}
