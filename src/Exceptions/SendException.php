<?php

declare(strict_types=1);

namespace Adiuta\SMS\Exceptions;

class SendException extends BaseException
{
    public function __construct(string $message = "Send failed, Please check the logs for more details", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
