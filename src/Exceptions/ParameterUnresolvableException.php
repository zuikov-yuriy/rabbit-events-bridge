<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Exception;

class ParameterUnresolvableException extends Exception
{
    public function __construct($parameterName, $method, $className, $code = 0, Throwable $previous = null)
    {
        $message = "Parameter {$parameterName} of method {$method} in class {$className} can't be resolved! It is a simple type!";
        parent::__construct($message, $code, $previous);
    }

}