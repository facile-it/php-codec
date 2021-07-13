<?php

declare(strict_types=1);

namespace Tests\Facile\PhpCodec\Internal\Combinators;

use Eris\Generator as g;
use Eris\TestTrait;
use Facile\PhpCodec\Codec;
use Facile\PhpCodec\Codecs;
use Facile\PhpCodec\Decoders;
use function Facile\PhpCodec\destructureIn;
use Tests\Facile\PhpCodec\BaseTestCase;
use Tests\Facile\PhpCodec\GeneratorUtils;

/** @psalm-suppress PropertyNotSetInConstructor */
class LiteralCodecTest extends BaseTestCase
{
    use TestTrait;

    public function testLaws(): void
    {
        /** @psalm-suppress UndefinedFunction */
        $this
            ->forAll(
                g\bind(
                    g\oneOf(
                        g\int(),
                        g\string(),
                        g\bool()
                    ),
                    /**
                     * @psalm-param int | string | bool $literal
                     * @psalm-suppress MixedReturnStatement
                     * @psalm-suppress MixedInferredReturnType
                     *
                     * @param mixed $literal
                     */
                    function ($literal): g {
                        return g\tuple(
                            g\constant(Codecs::fromDecoder(Decoders::literal($literal))),
                            g\oneOf(
                                GeneratorUtils::scalar(),
                                g\constant($literal)
                            ),
                            g\constant($literal)
                        );
                    }
                )
            )
            ->then(destructureIn(
                /**
                 * @psalm-param int | string | bool $u
                 * @psalm-param int | string | bool $a
                 *
                 * @param mixed $u
                 * @param mixed $a
                 */
                function (Codec $codec, $u, $a): void {
                    self::codecLaws($codec)($u, $a);
                }
            ));
    }
}
