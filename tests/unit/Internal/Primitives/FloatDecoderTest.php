<?php

declare(strict_types=1);

namespace Tests\Unit\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\FloatDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class FloatDecoderTest extends TestCase
{
    public function testValidFloat(): void
    {
        $decoder = new FloatDecoder();
        $result = $decoder->decode(3.14);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidFloatProvider
     */
    public function testInvalidValues($invalidValue): void
    {
        $decoder = new FloatDecoder();
        $result = $decoder->decode($invalidValue);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function invalidFloatProvider(): array
    {
        return [
            'int' => [42],
            'string' => ['3.14'],
            'bool' => [true],
            'array' => [[3.14]],
            'null' => [null],
        ];
    }
}
