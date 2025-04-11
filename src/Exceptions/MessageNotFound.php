<?php

declare(strict_types=1);

namespace Adiuta\SMS\Exceptions;

class MessageNotFound extends BaseException
{
    public function __construct(string $message = "Message was not found", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
