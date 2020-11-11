<?php

namespace TheP6\RabbitEventsBridge;

use TheP6\RabbitEventsBridge\MessageRouter\MessageRouter;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void add(string $routingKey, callable|string $handler)
 *
 * @see MessageRouter
 */
class MessageRouterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MessageRouter::class;
    }
}