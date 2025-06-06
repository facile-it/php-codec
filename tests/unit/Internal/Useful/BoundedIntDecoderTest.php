<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Facile\PhpCodec\Internal\Useful\BoundedIntDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class BoundedIntDecoderTest extends TestCase
{
    /**
     * @dataProvider validIntProvider
     *
     * @param mixed $input
     */
    public function testValidInts($input): void
    {
        $decoder = new BoundedIntDecoder(10, 20);
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidIntProvider
     *
     * @param mixed $input
     */
    public function testInvalidInts($input): void
    {
        $decoder = new BoundedIntDecoder(10, 20);
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function validIntProvider(): array
    {
        return [
            'lower bound' => [10],
            'middle' => [15],
            'upper bound' => [20],
        ];
    }

    public static function invalidIntProvider(): array
    {
        return [
            'below range' => [9],
            'above range' => [21],
            'string' => ['15'],
            'float' => [15.0],
            'null' => [null],
            'bool' => [true],
            'array' => [[15]],
            'object' => [new \stdClass()],
        ];
}
