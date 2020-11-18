<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Exception;
use TheP6\RabbitEventsBridge\MessageRouter\MessageRouter;
use Throwable;

class UnknownRoutingKeyException extends Exception
{
    public function __construct(string $routingKey, MessageRouter $messageRouter, $code = 0, Throwable $previous = null)
    {
        $message = "{$routingKey} is not present in message-broker routes! " . print_r($messageRouter->getHandlerMap(), true);
        parent::__construct($message, $code, $previous);
    }

}