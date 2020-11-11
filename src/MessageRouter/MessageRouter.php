<?php

namespace TheP6\RabbitEventsBridge\MessageRouter;

class MessageRouter
{
    protected array $handlerMap  = [];

    protected HandlerResolver $handlerResolver;

    public function __construct(HandlerResolver $handlerResolver)
    {
        $this->handlerResolver = $handlerResolver;
    }

    public function add(string $routingKey, $handler)
    {
        if (is_callable($handler)) {
            $this->handlerMap[$routingKey] = $handler;
            return;
        }

        if (is_string($handler)) {
            $handler = explode('@', $handler);

            if (count($handler) === 2) {
                $this->handlerMap[$routingKey] = [
                    'className' => $handler[0],
                    'method'    => $handler[1],
                ];
            }
            return;
        }

        throw new \InvalidArgumentException("{$handler} is not callable!");
    }

    public function handle(string $routingKey, array $payload)
    {
        if (empty($this->handlerMap[$routingKey])) {
            throw new \InvalidArgumentException("Message broker. {$routingKey} can't be handled! Routing key is not defined!");
        }

        $this->handlerResolver->setCurrentPayload($payload);
        $this->handlerResolver->setCurrentRoutingKey($routingKey);

        $handler = $this->handlerMap[$routingKey];

        //resolve callable
        if (is_callable($handler)) {
            $handlerInvoker = new \ReflectionFunction($handler);
            $resolvedParameters = $this->handlerResolver->resolveParameters($handlerInvoker);
            $handlerInvoker->invokeArgs($resolvedParameters);
            return;
        }

        list($controller, $handlerInvoker) = $this->handlerResolver->resolveControllerHandler($handler);
        $parameters = $this->handlerResolver->resolveParameters($handlerInvoker);
        $handlerInvoker->invokeArgs($controller, $parameters);
    }
}