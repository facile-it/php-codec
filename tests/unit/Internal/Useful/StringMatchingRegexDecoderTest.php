<?php declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Useful;

use Eris\Generator;
use Eris\TestTrait;
use Facile\PhpCodec\Decoder;
use Facile\PhpCodec\Decoders;
use Facile\PhpCodec\Validation\ValidationFailures;
use Tests\Facile\PhpCodec\BaseTestCase;
use function Facile\PhpCodec\destructureIn;

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
            ->limitTo(1000)
            ->forAll(
                Generator\bind(
                    Generator\elements([
                        ['/^\d{2,5}$/', '^\d{2,5}$'],
                        ['/^\w$/', '^\w$'],
                        ['/^[a-zA-Z0-9]{2,3}$/', '^[A-Z]{3}$'],
                        ['/^[a-zA-Z0-9]{2,3}$/', '^[a-zA-Z0-9]{2,3}$'],
                    ]),
                    destructureIn(function (string $pcreRegex, string $generatorRegex) {
                        return Generator\tuple(
                            Decoders::stringMatchingRegex($pcreRegex),
                            Generator\regex($generatorRegex)
                        );
                    })
                )
            )
            ->then(destructureIn(function (Decoder $d, string $i) {
                self::asserSuccessSameTo(
                    $i,
                    $d->decode($i)
                );
            }));
    }
}
