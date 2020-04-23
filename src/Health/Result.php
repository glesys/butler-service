<?php

namespace Butler\Service\Health;

class Result
{
    public const OK = 'ok';
    public const WARNING = 'warning';
    public const CRITICAL = 'critical';
    public const UNKNOWN = 'unknown';

    public string $message;
    public string $status;
    public $value = null;

    private function __construct(string $message, string $status)
    {
        $this->message = $message;
        $this->status = $status;
    }

    public static function ok(string $message): self
    {
        return new static($message, static::OK);
    }

    public static function warning(string $message): self
    {
        return new static($message, static::WARNING);
    }

    public static function critical(string $message): self
    {
        return new static($message, static::CRITICAL);
    }

    public static function unknown(string $message): self
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
        switch ($this->status) {
            case static::CRITICAL:
                return 3;
            case static::WARNING:
                return 2;
            case static::OK:
                return 1;
            case static::UNKNOWN:
            default:
                return 0;
        }
    }
}
