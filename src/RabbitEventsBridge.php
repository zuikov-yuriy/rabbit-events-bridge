<?php


namespace App\MessageBroker;

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
        try {
            $this->messageRouter->handle(
                $routingKey,
                $this->extractPayload($payload)
            );
        } catch (ValidationException $exception) {
            Log::info($exception->getMessage() . ". Errors: " . print_r($exception->errors(), true));
        } catch (Exception $exception) {
            Log::info("Exception occurred: {$exception->getMessage()}");
            Log::info("Exception stack-trace: {$exception->getTraceAsString()}");
        }
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