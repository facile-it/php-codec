<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\CallableDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class CallableDecoderTest extends TestCase
{
    /**
     * @dataProvider validCallableProvider
     *
     * @param mixed $input
     */
    public function testValidCallables($input): void
    {
        $decoder = new CallableDecoder();
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidCallableProvider
     *
     * @param mixed $input
     */
    public function testInvalidCallables($input): void
    {
        $decoder = new CallableDecoder();
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function validCallableProvider(): array
    {
        return [
            'anonymous function' => [fn() => null],
            'named function' => ['strlen'],
            'static method as array' => [[self::class, 'helperStatic']],
        ];
    }

    public static function invalidCallableProvider(): array
    {
        return [
            'int' => [123],
            'string' => ['not_callable'],
            'array of strings' => [['a', 'b']],
            'null' => [null],
            'object' => [new \stdClass()],
        ];
    }

    public static function helperStatic(): void
    {
        // Metodo statico valido per test
    }
}
