<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generators;
use Eris\TestTrait;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Internal\FunctionUtils;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;

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
                    fn(int $x): string => (string) $x,
                    Generators::choose(10, 99_999)
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
                    Generators::choose(10, 99_999),
                    Generators::elements(['a', 'b', 'c', 'd', 'e', 'f', 'g'])
                )
            )
            ->then(FunctionUtils::destructureIn(function (int $x, string $a) {
                $in = \sprintf('%d%s', $x, $a);
                self::asserSuccessSameTo(
                    $in,
                    Decoders::stringMatchingRegex('/^\d{2,5}\w$/')->decode($in)
                );
            }));
    }
}
