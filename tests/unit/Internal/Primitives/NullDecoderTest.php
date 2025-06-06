<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\NullDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class NullDecoderTest extends TestCase
{
    public function testValidNull(): void
    {
        $decoder = new NullDecoder();
        $result = $decoder->decode(null);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
        $this->assertSame(null, $result->getValue());
    }

    /**
     * @dataProvider provideInvalidValues
     *
     * @param mixed $value
     */
    public function testInvalidValues($value): void
    {
        $decoder = new NullDecoder();
        $result = $decoder->decode($value);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function provideInvalidValues(): array
    {
        return [
            'int' => [42],
            'string' => ['null'],
            'bool' => [true],
            'float' => [3.14],
            'array' => [[null]],
            'object' => [new \stdClass()],
            'callable' => [fn() => null],
        ];
    }
}
