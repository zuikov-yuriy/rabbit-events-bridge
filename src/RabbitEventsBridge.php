<?php


namespace TheP6\RabbitEventsBridge;

use TheP6\RabbitEventsBridge\Exceptions\UnknownRoutingKeyException;
use TheP6\RabbitEventsBridge\MessageRouter\MessageRouter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

/**
 * Class RabbitEventsBridge
 *
 * Is used as listener in rabbit-events library setup
 */
class RabbitEventsBridge
{
    protected MessageRouter $messageRouter;

    public function __construct(MessageRouter $messageRouter)
    {
        $this->messageRouter = $messageRouter;
    }

    public function handle(string $routingKey, array $payload)
    {
        $this->messageRouter->handle(
            $routingKey,
            $this->extractPayload($payload)
        );
    }

    //fixing typo/bug in rabbitevents library
    protected function extractPayload(array $payload)
    {
        if (count($payload) === 1 && !empty($payload[0])) {
            return $payload[0];
        }

        return $payload;
    }
}
