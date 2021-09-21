<?php

namespace TheP6\RabbitEventsBridge\Exceptions;

use Illuminate\Validation\ValidationException;

class MessageValidationException extends ValidationException
{
    public static function createFromValidationException(ValidationException $e)
    {
        return new MessageValidationException($e->validator, $e->response, $e->errorBag);
    }
}
