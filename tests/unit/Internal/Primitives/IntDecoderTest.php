<?php

declare(strict_types=1);

namespace Tests\Unit\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\IntDecoder;
use Facile\PhpCodec\Validation\ValidationSuccess;
use Facile\PhpCodec\Validation\ValidationFailures;
use PHPUnit\Framework\TestCase;

final class IntDecoderTest extends TestCase
{
    public function testValidInt(): void
    {
        $decoder = new IntDecoder();
        $result = $decoder->decode(42);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidIntProvider
     */
    public function testInvalidValues(mixed $input): void
    {
        $decoder = new IntDecoder();
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function invalidIntProvider(): array
    {
        return [
            'float' => [3.14],
            'string' => ['42'],
            'bool' => [true],
            'null' => [null],
            'array' => [[1]],
            'object' => [new \stdClass()],
        ];
    }
}
