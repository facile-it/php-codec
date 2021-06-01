<?php declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

class IntersectionDecoderTest extends BaseTestCase
{
    public function testStringIntersection(): void
    {
        $d = Decoders::intersection(
            Decoders::stringMatchingRegex('/^\d{2,4}$/'),
            Decoders::stringMatchingRegex('/^\d{3}$/')
        );

        self::asserSuccessSameTo(
            '123',
            $d->decode('123')
        );

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode('12')
        );

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode('1234')
        );
    }
}
