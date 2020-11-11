<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Exception;
use TheP6\RabbitEventsBridge\Controllers\RabbitEventsBridgeController;
use Throwable;

class InvalidControllerException extends Exception
{
    public function __construct($className, $code = 0, Throwable $previous = null)
    {
        $message = "{$className} is not an instance of ".RabbitEventsBridgeController::class;
        parent::__construct($message, $code, $previous);
    }

}