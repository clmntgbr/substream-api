<?php

namespace App\Exception;

class InvalidVideoMimeTypeException extends BusinessException
{
    public function __construct(string $mimeType)
    {
        parent::__construct("Invalid video mime type: {$mimeType}", 400);
    }
}
