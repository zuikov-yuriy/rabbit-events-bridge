<?php

namespace TheP6\RabbitEventsBridge\MessageRouter;

use ReflectionClass;
use ReflectionParameter;
use TheP6\RabbitEventsBridge\Controllers\RabbitEventsBridgeController;
use TheP6\RabbitEventsBridge\Exceptions\InvalidControllerException;
use TheP6\RabbitEventsBridge\Exceptions\MethodInaccessibleException;
use TheP6\RabbitEventsBridge\Exceptions\MethodNowFoundException;
use TheP6\RabbitEventsBridge\Exceptions\ParameterUnresolvableException;
use TheP6\RabbitEventsBridge\Message;
use ReflectionFunctionAbstract;

/**
 * Resolve handler parameters
 *
 * Class HandlerResolver
 */
class HandlerResolver
{
    protected ?string $currentRoutingKey = null;

    protected ?array $currentPayload = null;

    public function setCurrentPayload(array $payload)
    {
        $this->currentPayload = $payload;
    }

    public function setCurrentRoutingKey(string $routingKey)
    {
        $this->currentRoutingKey = $routingKey;
    }

    protected function currentPayload(): array
    {
        if (null === $this->currentPayload) {
            throw new \InvalidArgumentException("Current payload key is not set!");
        }

        return $this->currentPayload;
    }

    protected function currentRoutingKey(): string
    {
        if (null === $this->currentRoutingKey) {
            throw new \InvalidArgumentException("Current routing key is not set!");
        }

        return $this->currentRoutingKey;
    }

    public function resolveControllerHandler(array $handlerDescription): array
    {
        $controller = resolve($handlerDescription['className']);

        if (!($controller instanceof RabbitEventsBridgeController)) {
            throw new InvalidControllerException($handlerDescription['className']);
        }

        $reflection = new ReflectionClass($controller);

        if (!$reflection->hasMethod($handlerDescription['method'])) {
            throw new MethodNowFoundException($handlerDescription['className'], $handlerDescription['method']);
        }

        $method = $reflection->getMethod($handlerDescription['method']);

        if ($method->isPrivate() || $method->isProtected()) {
            throw new MethodInaccessibleException($handlerDescription['className'], $handlerDescription['method']);
        }

        return [$controller, $method];
    }

    public function resolveParameters(ReflectionFunctionAbstract $handler): array
    {
        $parameters = $handler->getParameters();
        $resolvedParameters = [];

        foreach ($parameters as $parameter) {
            if (null === $parameter->getClass()) {
                throw new ParameterUnresolvableException(
                    $parameter->getName(),
                    $handler['method'],
                    $handler['className']
                );
            }

            if ($this->doesExtendFromMessage($parameter)) {
                $resolvedParameters[] = $this->resolveMessage(
                    $parameter->getClass()->getName(),
                    $this->currentRoutingKey(),
                    $this->currentPayload()
                );
                continue;
            }
            $resolvedParameters[] = resolve($parameter->getClass()->getName());
        }

        return $resolvedParameters;
    }

    private function doesExtendFromMessage(ReflectionParameter $reflectionParameter): bool
    {
        if ($reflectionParameter->getClass()->getName() === Message::class) {
            return true;
        }

        return $reflectionParameter->getClass()->isSubclassOf(Message::class);
    }

    private function resolveMessage(string $messageClass, string $routingKey, array $payload)
    {
        if ($messageClass === Message::class) {
            return new Message($routingKey, $payload);
        }

        $messageClassReflection = new ReflectionClass($messageClass);
        $constructorParameters = $messageClassReflection->getConstructor()->getParameters();

        $resolvedParameters = [];

        foreach ($constructorParameters as $parameter) {
            if ($parameter->isArray() && $parameter->name === 'payload') {
                $resolvedParameters[] = $payload;
                continue;
            }

            if (null === $parameter->getClass()) {
                if ($parameter->name === 'routingKey') {
                    $resolvedParameters[] = $routingKey;
                    continue;
                }

                throw new ParameterUnresolvableException(
                    $parameter->getName(),
                    'constructor',
                    $messageClass
                );
            }

            $resolvedParameters[] = resolve($parameter->getClass()->getName());
        }

        return $messageClassReflection->newInstanceArgs($resolvedParameters);
    }
}