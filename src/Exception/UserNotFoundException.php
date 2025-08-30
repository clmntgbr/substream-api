<?php

namespace App\Exception;

class UserNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('User not found', 400);
    }
}
