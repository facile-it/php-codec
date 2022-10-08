<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use function Facile\PhpCodec\destructureIn;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

/** @psalm-suppress PropertyNotSetInConstructor */
class StringMatchingRegexDecoderTest extends BaseTestCase
{
    use TestTrait;

    public function testDecode(): void
    {
        $d = Decoders::stringMatchingRegex('/^\d{2,5}$/');

        self::assertInstanceOf(
            ValidationFailures::class,
            $d->decode('hello')
        );

        $this
            ->forAll(
                Generators::map(
                    function (int $x): string {
                        return (string) $x;
                    },
                    Generators::choose(10, 99999)
                )
            )
            ->then(function (string $in): void {
                self::asserSuccessSameTo(
                    $in,
                    Decoders::stringMatchingRegex('/^\d{2,5}$/')->decode($in)
                );
            });

        $this
            ->forAll(
                Generators::tuple(
                    Generators::choose(10, 99999),
                    Generators::elements(['a', 'b', 'c', 'd', 'e', 'f', 'g'])
                )
            )
            ->then(destructureIn(function (int $x, string $a) {
                $in = \sprintf('%d%s', $x, $a);
                self::asserSuccessSameTo(
                    $in,
                    Decoders::stringMatchingRegex('/^\d{2,5}\w$/')->decode($in)
                );
            }));
    }
}
