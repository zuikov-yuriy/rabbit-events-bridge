<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Exception;

class MethodNowFoundException extends Exception
{
    public function __construct($className, $method, $code = 0, Throwable $previous = null)
    {
        $message = "Method {$method} of class {$className} is not public!";
        parent::__construct($message, $code, $previous);
    }
}