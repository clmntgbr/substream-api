<?php

namespace App\Exception;

class UploadVideoException extends BusinessException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }
}
