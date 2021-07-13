<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generator;
use Eris\TestTrait;
use Facile\PhpCodec\Decoder;
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

        /** @psalm-suppress UndefinedFunction */
        $this
            ->limitTo(1000)
            ->forAll(
                Generator\bind(
                    Generator\elements([
                        ['/^\d{2,5}$/', '^\d{2,5}$'],
                        ['/^\w$/', '^\w$'],
                        ['/^[a-zA-Z0-9]{2,3}$/', '^[A-Z]{3}$'],
                        ['/^[a-zA-Z0-9]{2,3}$/', '^[a-zA-Z0-9]{2,3}$'],
                    ]),
                    destructureIn(
                    /** @psalm-suppress MixedInferredReturnType */
                    function (string $pcreRegex, string $generatorRegex): Generator {
                        /**
                         * @psalm-suppress UndefinedFunction
                         * @psalm-suppress MixedReturnStatement
                         */
                        return Generator\tuple(
                            Decoders::stringMatchingRegex($pcreRegex),
                            Generator\regex($generatorRegex)
                        );
                    }
                    )
                )
            )
            ->then(destructureIn(function (Decoder $d, string $i): void {
                self::asserSuccessSameTo(
                    $i,
                    $d->decode($i)
                );
            }));
    }
}
