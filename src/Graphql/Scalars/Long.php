<?php

declare(strict_types=1);

namespace Butler\Service\Graphql\Scalars;

use BackedEnum;
use Butler\Graphql\Contracts\Scalar;
use GraphQL\Error\Error;
use GraphQL\Error\SerializationError;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Language\Printer;
use GraphQL\Utils\Utils;

class Long implements Scalar
{
    public function description(): string
    {
        return 'Signed 64-bit integer (-2^63 to 2^63-1), serialized as a JSON number.';
    }

    public function specifiedByURL(): string
    {
        return 'https://scalars.graphql.org/apollographql/long-v0.1.html';
    }

    public function serialize(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ($int = $this->intFromString($value)) !== null) {
            return $int;
        }

        if ($value instanceof BackedEnum && is_int($value->value)) {
            return $value->value;
        }

        throw new SerializationError('Long cannot represent value: ' . Utils::printSafe($value));
    }

    public function parseValue(mixed $value): int
    {
        // json_decode() turns integers outside the 64-bit range into floats, so is_int() is also the range check
        if (! is_int($value)) {
            throw new Error('Long cannot represent value: ' . Utils::printSafeJson($value));
        }

        return $value;
    }

    public function parseLiteral(ValueNode $valueNode, ?array $variables = null): int
    {
        if ($valueNode instanceof IntValueNode && ($int = $this->intFromString($valueNode->value)) !== null) {
            return $int;
        }

        throw new Error('Long cannot represent value: ' . Printer::doPrint($valueNode), $valueNode);
    }

    private function intFromString(string $value): ?int
    {
        if ($value === '-0') {
            return 0;
        }

        $int = (int) $value;

        return (string) $int === $value ? $int : null;
    }
}
