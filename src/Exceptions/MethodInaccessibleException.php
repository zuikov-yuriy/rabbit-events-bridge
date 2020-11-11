<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Exception;
use Throwable;

class MethodInaccessibleException extends Exception
{
    public function __construct($className, $method, $code = 0, Throwable $previous = null)
    {
        $message = "Method {$method} does not exist in {$className}";
        parent::__construct($message, $code, $previous);
    }

}