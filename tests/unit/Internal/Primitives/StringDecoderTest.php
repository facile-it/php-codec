<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\StringDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class StringDecoderTest extends TestCase
{
    public function testValidString(): void
    {
        $decoder = new StringDecoder();

        $result = $decoder->decode('ciao');

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidValuesProvider
     *
     * @param mixed $input
     */
    public function testInvalidValues($input): void
    {
        $decoder = new StringDecoder();
        $result = $decoder->decode($input);
        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function invalidValuesProvider(): array
    {
        return [
            'integer' => [123],
            'float' => [3.14],
            'boolean' => [true],
            'array' => [['not', 'a', 'string']],
            'object' => [new \stdClass()],
            'null' => [null],
        ];
    }
}
