<?php

declare(strict_types=1);

namespace Adiuta\SMS\Exceptions;

class BaseException extends \Exception
{
    public function __construct(string $message = "An error occurred", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
