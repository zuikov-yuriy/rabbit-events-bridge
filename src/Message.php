<?php

namespace TheP6\RabbitEventsBridge;

use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class Message
{
    protected array $payload = [];

    private string $routingKey;

    protected ValidatorContract $validator;

    public function __construct(string $routingKey, array $payload)
    {
        $this->payload = $payload;
        $this->routingKey = $routingKey;
        $this->validator = Validator::make(
            $payload,
            $this->rules(),
        );
    }

    public function validated(): array
    {
        return $this->validator->validate();
    }

    public function get(string $key, $default = null)
    {
        return $this->payload[$key] ?? $default;
    }

    public function all()
    {
        return $this->payload;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    protected function rules(): array
    {
        return [];
    }
}