<?php

declare(strict_types=1);

namespace Tests\Unit\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\MixedDecoder;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class MixedDecoderTest extends TestCase
{
    /**
     * @dataProvider provideValues
     */
    public function testAcceptsAnyValue(mixed $value): void
    {
        $decoder = new MixedDecoder();
        $result = $decoder->decode($value);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
        $this->assertSame($value, $result->getValue());
    }

    public static function provideValues(): array
    {
        return [
            'string' => ['hello'],
            'int' => [42],
            'float' => [3.14],
            'bool true' => [true],
            'bool false' => [false],
            'null' => [null],
            'array' => [[1, 2, 3]],
            'object' => [new \stdClass()],
            'callable' => [fn() => 'test'],
        ];
    }
}
