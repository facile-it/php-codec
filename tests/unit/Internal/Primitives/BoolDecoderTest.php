<?php

declare(strict_types=1);

namespace Tests\Unit\Internal\Primitives;

use Facile\PhpCodec\Internal\Primitives\BoolDecoder;
use Facile\PhpCodec\Validation\ValidationFailures;
use Facile\PhpCodec\Validation\ValidationSuccess;
use PHPUnit\Framework\TestCase;

final class BoolDecoderTest extends TestCase
{
    /**
     * @dataProvider validBoolProvider
     */
    public function testValidBools(mixed $input): void
    {
        $decoder = new BoolDecoder();
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationSuccess::class, $result);
    }

    /**
     * @dataProvider invalidBoolProvider
     */
    public function testInvalidBools(mixed $input): void
    {
        $decoder = new BoolDecoder();
        $result = $decoder->decode($input);

        $this->assertInstanceOf(ValidationFailures::class, $result);
    }

    public static function validBoolProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
        ];
    }

    public static function invalidBoolProvider(): array
    {
        return [
            'int' => [1],
            'string true' => ['true'],
            'string false' => ['false'],
            'null' => [null],
            'float' => [1.0],
            'array' => [[true]],
        ];
    }
}
