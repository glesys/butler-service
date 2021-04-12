<?php

namespace Butler\Service\Health;

class Result
{
    public const OK = 'ok';
    public const WARNING = 'warning';
    public const CRITICAL = 'critical';
    public const UNKNOWN = 'unknown';

    public $value = null;

    private function __construct(public string $message, public string $state)
    {
    }

    public static function ok(string $message): static
    {
        return new static($message, static::OK);
    }

    public static function warning(string $message): static
    {
        return new static($message, static::WARNING);
    }

    public static function critical(string $message): static
    {
        return new static($message, static::CRITICAL);
    }

    public static function unknown(string $message): static
    {
        return new static($message, static::UNKNOWN);
    }

    public function value()
    {
        if (func_num_args() === 1) {
            $this->value = func_get_arg(0);
        }

        return $this->value;
    }

    public function order(): int
    {
        return match ($this->state) {
            static::CRITICAL => 3,
            static::WARNING => 2,
            static::OK => 1,
            static::UNKNOWN, 'default' => 0,
        };
    }
}
