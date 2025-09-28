<?php

namespace App\Exception;

class ProcessorException extends BusinessException
{
    public function __construct(string $message)
    {
        parent::__construct('Processor error: '.$message, 400);
    }
}
