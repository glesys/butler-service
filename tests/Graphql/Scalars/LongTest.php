<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Graphql\Scalars;

use Butler\Graphql\Contracts\Scalar;
use Butler\Service\Graphql\Scalars\Long;
use Butler\Service\Tests\TestCase;
use GraphQL\Error\Error;
use GraphQL\Error\SerializationError;
use GraphQL\Language\Parser;
use PHPUnit\Framework\Attributes\DataProvider;

class LongTest extends TestCase
{
    public function test_implements_scalar_contract()
    {
        $this->assertInstanceOf(Scalar::class, new Long());
    }

    #[DataProvider('validSerializeProvider')]
    public function test_serialize(mixed $value, int $expected)
    {
        $this->assertSame($expected, (new Long())->serialize($value));
    }

    public static function validSerializeProvider()
    {
        return [
            'zero' => [0, 0],
            'above 32-bit' => [5368709120, 5368709120],
            'max' => [PHP_INT_MAX, PHP_INT_MAX],
            'min' => [PHP_INT_MIN, PHP_INT_MIN],
            'integer string' => ['5368709120', 5368709120],
            'negative integer string' => ['-5368709120', -5368709120],
            'int backed enum' => [IntBackedEnum::Big, 5368709120],
        ];
    }

    #[DataProvider('invalidSerializeProvider')]
    public function test_serialize_throws_on_invalid_value(mixed $value)
    {
        $this->expectException(SerializationError::class);

        (new Long())->serialize($value);
    }

    public static function invalidSerializeProvider()
    {
        return [
            'float' => [1.5],
            'integral float' => [1.0],
            'string above max' => ['9223372036854775808'],
            'non-numeric string' => ['foo'],
            'decimal string' => ['1.0'],
            'exponent string' => ['1e6'],
            'leading zero string' => ['042'],
            'plus sign string' => ['+1'],
            'boolean' => [true],
            'null' => [null],
        ];
    }

    #[DataProvider('validParseValueProvider')]
    public function test_parse_value(int $value)
    {
        $this->assertSame($value, (new Long())->parseValue($value));
    }

    public static function validParseValueProvider()
    {
        return [
            'zero' => [0],
            'above 32-bit' => [5368709120],
            'max' => [PHP_INT_MAX],
            'min' => [PHP_INT_MIN],
        ];
    }

    #[DataProvider('invalidParseValueProvider')]
    public function test_parse_value_throws_on_invalid_value(mixed $value)
    {
        $this->expectException(Error::class);

        (new Long())->parseValue($value);
    }

    public static function invalidParseValueProvider()
    {
        return [
            // json_decode() turns integers outside the 64-bit range into floats
            'above max as decoded from json' => [json_decode('9223372036854775808')],
            'float' => [1.5],
            'integral float' => [1.0],
            'integer string' => ['5368709120'],
            'boolean' => [true],
        ];
    }

    #[DataProvider('validParseLiteralProvider')]
    public function test_parse_literal(string $literal, int $expected)
    {
        $this->assertSame($expected, (new Long())->parseLiteral(Parser::parseValue($literal)));
    }

    public static function validParseLiteralProvider()
    {
        return [
            'zero' => ['0', 0],
            'negative zero' => ['-0', 0],
            'above 32-bit' => ['5368709120', 5368709120],
            'max' => ['9223372036854775807', PHP_INT_MAX],
            'min' => ['-9223372036854775808', PHP_INT_MIN],
        ];
    }

    #[DataProvider('invalidParseLiteralProvider')]
    public function test_parse_literal_throws_on_invalid_value(string $literal)
    {
        $this->expectException(Error::class);

        (new Long())->parseLiteral(Parser::parseValue($literal));
    }

    public static function invalidParseLiteralProvider()
    {
        return [
            'above max' => ['9223372036854775808'],
            'below min' => ['-9223372036854775809'],
            'float' => ['1.0'],
            'exponent' => ['1E7'],
            'string' => ['"5368709120"'],
            'boolean' => ['true'],
        ];
    }
}

enum IntBackedEnum: int
{
    case Big = 5368709120;
}
