<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Illuminate\Validation\ValidationException;

class MessageValidationException extends ValidationException
{
    protected $routingKey = null;

    protected $payload = null;

    public function setRoutingKey(string $routingKey)
    {
        $this->routingKey = $routingKey;
    }

    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public static function createFromValidationException(ValidationException $e, string $routingKey, array $payload)
    {
        $exception = new MessageValidationException($e->validator, $e->response, $e->errorBag);

        $exception->setRoutingKey($routingKey);

        $exception->setPayload($payload);

        return $exception;
    }
}
