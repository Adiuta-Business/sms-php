<?php

declare(strict_types=1);

namespace Adiuta\SMS\Exceptions;

class InvalidNumberException  extends BaseException
{

    public function __construct(string $mobile, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Invalid mobile number: $mobile", $code, $previous);
    }
}
