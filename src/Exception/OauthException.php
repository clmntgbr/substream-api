<?php

namespace App\Exception;

class OauthException extends BusinessException
{
    public function __construct(string $message)
    {
        parent::__construct('OAuth error: '.$message, 400);
    }
}
